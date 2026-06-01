<?php

namespace Database\Seeders;

use App\Models\SupportArticle;
use App\Models\SupportCategory;
use App\Models\SupportSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds support categories, sections, articles from the extracted JSON.
 *
 * Sourced from EzLicence's public help center as a structural reference.
 * Content is intended to be rewritten/edited by the client in their own voice
 * via the admin panel — this seeder just bootstraps the structure quickly.
 *
 * Run:  php artisan db:seed --class=SupportContentSeeder
 * Or to wipe and re-seed: pass --force after truncating manually.
 */
class SupportContentSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/support-content.json');
        if (! is_file($jsonPath)) {
            $this->command?->error("Missing seed file: $jsonPath");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($data)) {
            $this->command?->error('Invalid JSON in support-content.json');
            return;
        }

        // Avoid duplicates if re-run
        if (SupportCategory::exists()) {
            $this->command?->warn('Support content already exists — skipping. To re-seed, truncate support_articles, support_sections, support_categories first.');
            return;
        }

        DB::transaction(function () use ($data) {
            // ── Categories ──
            $catIconMap = [
                'learners'           => 'bi-mortarboard-fill',
                'driving-instructors'=> 'bi-car-front-fill',
            ];
            $catDescMap = [
                'learners'            => 'Everything you need to know as a learner — from booking your first lesson to managing payments.',
                'driving-instructors' => 'Help & tips for driving instructors using the platform — bookings, payments, account, growing your business.',
            ];

            $catIdMap = [];      // ezlicence id → new db id
            foreach ($data['categories'] ?? [] as $i => $cat) {
                $slug = Str::slug($cat['name']);
                $created = SupportCategory::create([
                    'name'        => $cat['name'],
                    'slug'        => $slug,
                    'description' => $catDescMap[$slug] ?? null,
                    'icon'        => $catIconMap[$slug] ?? 'bi-question-circle-fill',
                    'sort_order'  => $i + 1,
                    'is_active'   => true,
                ]);
                $catIdMap[$cat['id']] = $created->id;
            }

            // ── Sections ──
            $secIconMap = [
                'general-information'      => 'bi-info-circle',
                'manage-your-account'      => 'bi-person-gear',
                'manage-your-bookings'     => 'bi-calendar-check',
                'manage-your-payments'     => 'bi-credit-card',
                'ezlicence-policies'       => 'bi-shield-check',
                'getting-started-with-ezlicence' => 'bi-rocket-takeoff',
                'grow-your-income'         => 'bi-graph-up-arrow',
            ];

            $secIdMap = [];
            foreach ($data['sections'] ?? [] as $i => $sec) {
                if (! isset($catIdMap[$sec['category_id']])) continue;
                $slug = Str::slug($sec['name']);
                $created = SupportSection::create([
                    'category_id' => $catIdMap[$sec['category_id']],
                    'name'        => $sec['name'],
                    'slug'        => $slug,
                    'icon'        => $secIconMap[$slug] ?? 'bi-folder',
                    'sort_order'  => $i + 1,
                    'is_active'   => true,
                ]);
                $secIdMap[$sec['id']] = $created->id;
            }

            // ── Articles ──
            $count = 0;
            foreach ($data['articles'] ?? [] as $i => $art) {
                if (! isset($secIdMap[$art['section_id']])) continue;
                $title = $this->cleanText($art['title']);
                $content = $this->rewriteEzLicenceMentions($art['content']);
                if (strlen(strip_tags($content)) < 30) {
                    continue;  // skip stubs
                }
                SupportArticle::create([
                    'section_id'   => $secIdMap[$art['section_id']],
                    'title'        => $title,
                    'slug'         => Str::slug($title) ?: $art['slug'],
                    'excerpt'      => $this->cleanText(Str::limit(strip_tags($content), 200)),
                    'content'      => $content,
                    'sort_order'   => $i + 1,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                $count++;
            }

            $this->command?->info("✔ Seeded " . count($catIdMap) . " categories, " . count($secIdMap) . " sections, $count articles.");
        });
    }

    /** Clean common HTML entity quirks. */
    private function cleanText(string $s): string
    {
        return html_entity_decode(strip_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Rewrite obvious "EzLicence" mentions to "Secure Licence" so the content
     * fits the new brand. Image references that point to article_attachments
     * are rewritten to placeholders the admin can replace.
     */
    private function rewriteEzLicenceMentions(string $html): string
    {
        // ── First: truncate Zendesk article-footer junk (share/votes/comments/related) ──
        // These leaked through the original extractor. Re-using the canonical cleaner.
        $html = (new \App\Console\Commands\CleanSupportArticlesCommand())->cleanContent($html);

        // Brand swap
        $html = preg_replace('/\bEzLicence\b/i', 'Secure Licence', $html);
        $html = preg_replace('/\bezlicence\.com\.au\b/i', 'securelicence.com', $html);
        // Image src → placeholder (admin will re-upload)
        $html = preg_replace_callback(
            '/<img[^>]*src="([^"]+)"[^>]*>/i',
            function ($m) {
                // Skip remote images; just leave them out (broken on new site)
                return '<div class="alert alert-light border my-3 small text-muted"><i class="bi bi-image"></i> Image placeholder — replace in admin panel</div>';
            },
            $html
        );
        // Strip Zendesk-specific anchor links pointing to .html files (they'll 404)
        $html = preg_replace_callback(
            '/<a\s+[^>]*href="[^"]*\.html[^"]*"[^>]*>([^<]+)<\/a>/i',
            fn ($m) => $m[1],
            $html
        );
        // Tidy
        $html = preg_replace('/\s+/', ' ', $html);
        return trim($html);
    }
}
