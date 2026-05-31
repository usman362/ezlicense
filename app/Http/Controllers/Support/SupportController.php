<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportArticle;
use App\Models\SupportArticleFeedback;
use App\Models\SupportCategory;
use App\Models\SupportSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public-facing help center pages — served on the `support.` subdomain.
 *
 * Routes:
 *   GET  /                                      → home (search + categories)
 *   GET  /categories/{category}                 → category page (sections)
 *   GET  /sections/{section}                    → section page (articles)
 *   GET  /articles/{article}                    → article detail
 *   GET  /search?q=...                          → search results
 *   POST /articles/{article}/feedback           → helpful Y/N
 */
class SupportController extends Controller
{
    /**
     * Home — categories grid + popular articles + search bar.
     */
    public function home(): View
    {
        $categories = SupportCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount(['articles as articles_count' => fn ($q) => $q->where('is_published', true)])
            ->get();

        $popular = SupportArticle::where('is_published', true)
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        return view('support.home', compact('categories', 'popular'));
    }

    /**
     * Category landing — list of sections and any direct articles.
     */
    public function category(SupportCategory $category): View
    {
        abort_unless($category->is_active, 404);

        $sections = $category->sections()
            ->withCount(['articles as articles_count' => fn ($q) => $q->where('is_published', true)])
            ->with(['articles' => fn ($q) => $q->select('id', 'section_id', 'title', 'slug')->limit(5)])
            ->get();

        return view('support.category', compact('category', 'sections'));
    }

    /**
     * Section page — full article list within the section.
     */
    public function section(SupportSection $section): View
    {
        abort_unless($section->is_active, 404);
        $section->loadMissing('category');

        $articles = $section->articles()->paginate(20);

        return view('support.section', compact('section', 'articles'));
    }

    /**
     * Article detail — content + related + feedback widget.
     */
    public function article(SupportArticle $article): View
    {
        abort_unless($article->is_published, 404);
        $article->loadMissing('section.category');

        // Increment view count
        $article->increment('views_count');

        $related = SupportArticle::where('section_id', $article->section_id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->limit(5)
            ->get();

        return view('support.article', compact('article', 'related'));
    }

    /**
     * Search across published articles.
     */
    public function search(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $results = collect();

        if (strlen($q) >= 2) {
            // Try fulltext first, fall back to LIKE
            try {
                $results = SupportArticle::where('is_published', true)
                    ->whereRaw('MATCH(title, content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$q])
                    ->with('section.category')
                    ->limit(50)
                    ->get();
            } catch (\Throwable $e) {
                $results = SupportArticle::where('is_published', true)
                    ->where(function ($qq) use ($q) {
                        $qq->where('title', 'like', "%{$q}%")
                            ->orWhere('content', 'like', "%{$q}%")
                            ->orWhere('excerpt', 'like', "%{$q}%");
                    })
                    ->with('section.category')
                    ->limit(50)
                    ->get();
            }
        }

        return view('support.search', compact('q', 'results'));
    }

    /**
     * Helpful Yes/No vote on an article. Soft de-dupe by IP within 24h.
     */
    public function feedback(Request $request, SupportArticle $article)
    {
        $request->validate([
            'is_helpful' => 'required|boolean',
            'comment'    => 'nullable|string|max:500',
        ]);

        $ip = $request->ip();
        $already = SupportArticleFeedback::where('article_id', $article->id)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (! $already) {
            SupportArticleFeedback::create([
                'article_id' => $article->id,
                'is_helpful' => (bool) $request->input('is_helpful'),
                'ip_address' => $ip,
                'user_id'    => auth()->id(),
                'comment'    => $request->input('comment'),
            ]);
            if ($request->boolean('is_helpful')) {
                $article->increment('helpful_yes_count');
            } else {
                $article->increment('helpful_no_count');
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'already_voted' => $already]);
        }
        return back()->with('message', 'Thanks for your feedback!');
    }
}
