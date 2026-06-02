<?php

namespace App\Console\Commands;

use App\Models\SupportArticle;
use App\Models\SupportCategory;
use App\Models\SupportSection;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * One-shot cleanup: strip Zendesk's article-footer junk that leaked into seeded
 * article bodies (share buttons, Yes/No vote widget, "Have more questions?",
 * "Return to top", related-articles list, comments section).
 *
 * Run once after seeding:  php artisan support:clean-articles
 */
class CleanSupportArticlesCommand extends Command
{
    protected $signature = 'support:clean-articles {--dry-run : Show counts without saving}';
    protected $description = 'Strip leftover Zendesk footer junk (share/vote/comments/related) from seeded support article bodies';

    /**
     * Marker substrings — the EARLIEST occurrence of any of these in an article
     * marks where junk starts. We truncate the content there.
     */
    private array $junkMarkers = [
        // Wrapper blocks
        '<div class="article-footer"',
        '<div class="article-share"',
        '<div class="article-votes"',
        '<div class="article-more-questions"',
        '<div class="article-return-to-top"',
        '<div class="article-relatives"',
        '<section class="related-articles"',
        '<div class="article-comments"',
        '<section class="comments"',
        // Bare child elements (when the wrapper has been stripped but children remain)
        '<ul class="share"',
        '<ul class="meta-group"',
        'class="share-facebook"',
        'class="share-twitter"',
        'class="share-linkedin"',
        'class="article-vote-up"',
        'class="article-vote-down"',
        'class="article-votes-question"',
        'class="article-votes-controls"',
        'class="related-articles-title"',
        'class="comment-overview"',
        'class="comment-list"',
        'class="comment-callout"',
        'class="comment-heading"',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $count = SupportArticle::count();
        $this->info("Scanning {$count} articles + categories + sections...");

        $cleaned = 0;
        $titleFixed = 0;
        $unchanged = 0;
        $bytesRemoved = 0;
        $sectionsFixed = 0;
        $categoriesFixed = 0;

        // ── 1) Articles: content + title ──
        SupportArticle::chunk(50, function ($articles) use (&$cleaned, &$titleFixed, &$unchanged, &$bytesRemoved, $dry) {
            foreach ($articles as $a) {
                $beforeContent = (string) $a->content;
                $afterContent  = $this->cleanContent($beforeContent);

                $beforeTitle   = (string) $a->title;
                $afterTitle    = $this->rewriteBrand($beforeTitle);

                $contentChanged = $beforeContent !== $afterContent;
                $titleChanged   = $beforeTitle !== $afterTitle;

                if (! $contentChanged && ! $titleChanged) {
                    $unchanged++;
                    continue;
                }

                if ($contentChanged) {
                    $bytesRemoved += strlen($beforeContent) - strlen($afterContent);
                    $cleaned++;
                }
                if ($titleChanged) {
                    $titleFixed++;
                }

                if (! $dry) {
                    if ($contentChanged) {
                        $a->content = $afterContent;
                        $a->excerpt = Str::limit(strip_tags($afterContent), 200);
                    }
                    if ($titleChanged) {
                        $a->title = $afterTitle;
                        $a->slug = Str::slug($afterTitle);
                    }
                    $a->saveQuietly();
                }
            }
        });

        // ── 2) Sections: name + description ──
        foreach (SupportSection::all() as $s) {
            $newName = $this->rewriteBrand((string) $s->name);
            $newDesc = $this->rewriteBrand((string) $s->description);
            if ($newName !== $s->name || $newDesc !== $s->description) {
                $sectionsFixed++;
                if (! $dry) {
                    $s->name = $newName;
                    $s->description = $newDesc;
                    $s->slug = Str::slug($newName);
                    $s->saveQuietly();
                }
            }
        }

        // ── 3) Categories: name + description ──
        foreach (SupportCategory::all() as $c) {
            $newName = $this->rewriteBrand((string) $c->name);
            $newDesc = $this->rewriteBrand((string) $c->description);
            if ($newName !== $c->name || $newDesc !== $c->description) {
                $categoriesFixed++;
                if (! $dry) {
                    $c->name = $newName;
                    $c->description = $newDesc;
                    $c->slug = Str::slug($newName);
                    $c->saveQuietly();
                }
            }
        }

        $this->newLine();
        $this->info("Articles — content cleaned: {$cleaned}, titles rebranded: {$titleFixed}");
        $this->info("Articles — unchanged:       {$unchanged}");
        $this->info("Sections rebranded:         {$sectionsFixed}");
        $this->info("Categories rebranded:       {$categoriesFixed}");
        $this->info("Bytes removed:              " . number_format($bytesRemoved));

        if ($dry) {
            $this->warn('Dry run — no changes saved. Remove --dry-run to apply.');
        } else {
            $this->info('✔ Done. View an article to confirm.');
        }
        return self::SUCCESS;
    }

    /**
     * Truncate at the earliest junk marker, then strip a few more leftover patterns.
     */
    public function cleanContent(string $html): string
    {
        // 1) Find earliest junk marker — truncate there
        $earliest = PHP_INT_MAX;
        foreach ($this->junkMarkers as $marker) {
            $pos = stripos($html, $marker);
            if ($pos !== false && $pos < $earliest) {
                $earliest = $pos;
            }
        }
        if ($earliest !== PHP_INT_MAX) {
            $html = substr($html, 0, $earliest);
        }

        // 2) Strip any leftover bare <aside>...</aside>
        $html = preg_replace('/<aside\b[^>]*>.*?<\/aside>/is', '', $html);

        // 3) Strip text-based fallback markers (in case structure already partially stripped)
        $textMarkers = [
            'Was this article helpful?',
            'Have more questions?',
            'Return to top',
            'Related articles',
            'Comments<',
            '0 comments',
            'Please sign in to leave a comment',
        ];
        foreach ($textMarkers as $marker) {
            $pos = stripos($html, $marker);
            if ($pos !== false) {
                // Truncate at the start of the closest opening tag before the marker
                $cut = strrpos(substr($html, 0, $pos), '<');
                $html = $cut !== false ? substr($html, 0, $cut) : substr($html, 0, $pos);
            }
        }

        // 4) Strip orphan closing tags (after truncation we may have unclosed open tags
        //    or stray closers — minimal cleanup, don't try to balance perfectly).
        $html = preg_replace('/(<\/(article|section|aside|footer)>)+\s*$/i', '', $html);

        // 5) Tidy trailing whitespace
        $html = trim($html);

        // 6) Brand replacement (in case any new content slipped in)
        $html = $this->rewriteBrand($html);

        return $html;
    }

    /**
     * Brand swap — case-insensitive, also handles 'Ezlicence', 'EZLICENCE', 'ezlicence.com.au' etc.
     */
    public function rewriteBrand(string $text): string
    {
        if ($text === '') return '';
        $text = preg_replace('/\bezlicence\.com\.au\b/i', 'securelicence.com', $text);
        $text = preg_replace('/\bezlicence\b/i', 'Secure Licence', $text);
        // Tidy "Secure Licence Secure Licence" if double-replaced from prior runs
        $text = preg_replace('/\b(Secure Licence)(\s+\1\b)+/i', '$1', $text);
        return $text;
    }
}
