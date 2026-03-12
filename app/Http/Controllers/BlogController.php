<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with('category', 'author')->orderByDesc('published_at');

        if ($catSlug = $request->query('category')) {
            $cat = BlogCategory::where('slug', $catSlug)->first();
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
        $categories = BlogCategory::withCount(['posts' => fn ($q) => $q->published()])->orderBy('name')->get();
        $featured = BlogPost::published()->featured()->orderByDesc('published_at')->limit(3)->get();

        return view('frontend.pages.blog.index', compact('posts', 'categories', 'featured'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->with('category', 'author')->firstOrFail();
        $post->increment('views');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $categories = BlogCategory::withCount(['posts' => fn ($q) => $q->published()])->orderBy('name')->get();

        return view('frontend.pages.blog.show', compact('post', 'related', 'categories'));
    }
}
