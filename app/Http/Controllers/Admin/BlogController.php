<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /* ─── List ─── */
    public function index(Request $request)
    {
        return view('admin.pages.blog.index');
    }

    /** API: paginated list for JS table */
    public function list(Request $request)
    {
        $q = BlogPost::with(['category', 'author'])
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        if ($search = $request->query('search')) {
            $q->where('title', 'like', "%{$search}%");
        }
        if ($catId = $request->query('category_id')) {
            $q->where('category_id', $catId);
        }

        $posts = $q->paginate(15);

        return response()->json([
            'posts' => $posts->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'category' => $p->category?->name,
                'author' => $p->author?->name ?? 'Admin',
                'status' => $p->status,
                'is_featured' => $p->is_featured,
                'views' => $p->views,
                'published_at' => $p->published_at?->format('d M Y H:i'),
                'created_at' => $p->created_at->format('d M Y'),
            ]),
            'total' => $posts->total(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
        ]);
    }

    /* ─── Create / Edit form ─── */
    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.pages.blog.form', ['post' => null, 'categories' => $categories]);
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.pages.blog.form', ['post' => $blogPost, 'categories' => $categories]);
    }

    /* ─── Store ─── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $data['author_id'] = auth()->id();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('message', 'Blog post created successfully.');
    }

    /* ─── Update ─── */
    public function update(Request $request, BlogPost $blogPost)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($blogPost->featured_image && Storage::disk('public')->exists($blogPost->featured_image)) {
                Storage::disk('public')->delete($blogPost->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && ! $blogPost->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog.index')->with('message', 'Blog post updated successfully.');
    }

    /* ─── Delete ─── */
    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->featured_image && Storage::disk('public')->exists($blogPost->featured_image)) {
            Storage::disk('public')->delete($blogPost->featured_image);
        }
        $blogPost->delete();

        return response()->json(['message' => 'Post deleted.']);
    }

    /* ─── Toggle Featured ─── */
    public function toggleFeatured(BlogPost $blogPost)
    {
        $blogPost->update(['is_featured' => ! $blogPost->is_featured]);
        return response()->json(['is_featured' => $blogPost->is_featured]);
    }

    /* ══════════════ Categories ══════════════ */

    public function categories()
    {
        return view('admin.pages.blog.categories');
    }

    public function categoryList()
    {
        $cats = BlogCategory::withCount('posts')->orderBy('name')->get();
        return response()->json(['categories' => $cats]);
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        $data['slug'] = Str::slug($data['name']);

        if (BlogCategory::where('slug', $data['slug'])->exists()) {
            return response()->json(['error' => 'Category already exists.'], 422);
        }

        $cat = BlogCategory::create($data);
        return response()->json(['category' => $cat, 'message' => 'Category created.']);
    }

    public function categoryUpdate(Request $request, BlogCategory $blogCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        $data['slug'] = Str::slug($data['name']);

        if (BlogCategory::where('slug', $data['slug'])->where('id', '!=', $blogCategory->id)->exists()) {
            return response()->json(['error' => 'Category already exists.'], 422);
        }

        $blogCategory->update($data);
        return response()->json(['category' => $blogCategory, 'message' => 'Category updated.']);
    }

    public function categoryDestroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
