<?php

namespace App\Console\Commands;

use App\Models\SupportArticle;
use Illuminate\Console\Command;

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
        '<div class="article-footer"',
        '<div class="article-share"',
        '<div class="article-votes"',
        '<div class="article-more-questions"',
        '<div class="article-return-to-top"',
        '<div class="article-relatives"',
        '<section class="related-articles"',
        '<div class="article-comments"',
        '<section class="comments"',
        '<ul class="share"',
        '<ul class="meta-group"',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $count = SupportArticle::count();
        $this->info("Scanning {$count} articles...");

        $cleaned = 0;
        $unchanged = 0;
        $bytesRemoved = 0;

        SupportArticle::chunk(50, function ($articles) use (&$cleaned, &$unchanged, &$bytesRemoved, $dry) {
            foreach ($articles as $a) {
                $before = (string) $a->content;
                $after = $this->cleanContent($before);

                if ($before === $after) {
                    $unchanged++;
                    continue;
                }

                $bytesRemoved += strlen($before) - strlen($after);
                $cleaned++;

                if (! $dry) {
                    $a->content = $after;
                    // Refresh excerpt from the cleaned content
                    $a->excerpt = \Illuminate\Support\Str::limit(strip_tags($after), 200);
                    $a->saveQuietly();
                }
            }
        });

        $this->newLine();
        $this->info("Cleaned:   {$cleaned}");
        $this->info("Unchanged: {$unchanged}");
        $this->info("Bytes removed: " . number_format($bytesRemoved));

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

        return $html;
    }
}
