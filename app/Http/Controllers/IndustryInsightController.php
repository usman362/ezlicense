<?php

namespace App\Http\Controllers;

use App\Models\IndustryInsight;
use App\Models\IndustryInsightCategory;
use Illuminate\Http\Request;

class IndustryInsightController extends Controller
{
    public function index(Request $request)
    {
        $query = IndustryInsight::published()->with('category', 'author')->orderByDesc('published_at');

        if ($catSlug = $request->query('category')) {
            $cat = IndustryInsightCategory::where('slug', $catSlug)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9)->withQueryString();
        $categories = IndustryInsightCategory::withCount(['insights' => fn ($q) => $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->orderBy('name')->get();
        $featured = IndustryInsight::published()->featured()->orderByDesc('published_at')->limit(3)->get();

        return view('frontend.pages.industry-insights.index', compact('posts', 'categories', 'featured'));
    }

    public function show(string $slug)
    {
        $post = IndustryInsight::published()->where('slug', $slug)->with('category', 'author')->firstOrFail();
        $post->increment('views');

        $related = IndustryInsight::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $categories = IndustryInsightCategory::withCount(['insights' => fn ($q) => $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->orderBy('name')->get();

        return view('frontend.pages.industry-insights.show', compact('post', 'related', 'categories'));
    }
}
