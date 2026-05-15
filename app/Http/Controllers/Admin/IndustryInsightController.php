<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndustryInsight;
use App\Models\IndustryInsightCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IndustryInsightController extends Controller
{
    /* ─── List ─── */
    public function index(Request $request)
    {
        return view('admin.pages.industry-insights.index');
    }

    /** API: paginated list for JS table */
    public function list(Request $request)
    {
        $q = IndustryInsight::with(['category', 'author'])
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
        $categories = IndustryInsightCategory::orderBy('name')->get();
        return view('admin.pages.industry-insights.form', ['post' => null, 'categories' => $categories]);
    }

    public function edit(IndustryInsight $industryInsight)
    {
        $categories = IndustryInsightCategory::orderBy('name')->get();
        return view('admin.pages.industry-insights.form', ['post' => $industryInsight, 'categories' => $categories]);
    }

    /* ─── Store ─── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:industry_insight_categories,id',
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
            $data['featured_image'] = $request->file('featured_image')->store('industry-insights', 'public');
        }

        $data['author_id'] = auth()->id();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        IndustryInsight::create($data);

        return redirect()->route('admin.industry-insights.index')->with('message', 'Industry insight created successfully.');
    }

    /* ─── Update ─── */
    public function update(Request $request, IndustryInsight $industryInsight)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:industry_insight_categories,id',
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
            if ($industryInsight->featured_image && Storage::disk('public')->exists($industryInsight->featured_image)) {
                Storage::disk('public')->delete($industryInsight->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('industry-insights', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && ! $industryInsight->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $industryInsight->update($data);

        return redirect()->route('admin.industry-insights.index')->with('message', 'Industry insight updated successfully.');
    }

    /* ─── Delete ─── */
    public function destroy(IndustryInsight $industryInsight)
    {
        if ($industryInsight->featured_image && Storage::disk('public')->exists($industryInsight->featured_image)) {
            Storage::disk('public')->delete($industryInsight->featured_image);
        }
        $industryInsight->delete();

        return response()->json(['message' => 'Insight deleted.']);
    }

    /* ─── Toggle Featured ─── */
    public function toggleFeatured(IndustryInsight $industryInsight)
    {
        $industryInsight->update(['is_featured' => ! $industryInsight->is_featured]);
        return response()->json(['is_featured' => $industryInsight->is_featured]);
    }

    /* ══════════════ Categories ══════════════ */

    public function categories()
    {
        return view('admin.pages.industry-insights.categories');
    }

    public function categoryList()
    {
        $cats = IndustryInsightCategory::withCount('insights')->orderBy('name')->get();
        // Map insights_count → posts_count to keep the same JS shape as the blog system
        $cats->each(function ($c) {
            $c->posts_count = $c->insights_count;
        });
        return response()->json(['categories' => $cats]);
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        $data['slug'] = Str::slug($data['name']);

        if (IndustryInsightCategory::where('slug', $data['slug'])->exists()) {
            return response()->json(['error' => 'Category already exists.'], 422);
        }

        $cat = IndustryInsightCategory::create($data);
        return response()->json(['category' => $cat, 'message' => 'Category created.']);
    }

    public function categoryUpdate(Request $request, IndustryInsightCategory $industryInsightCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        $data['slug'] = Str::slug($data['name']);

        if (IndustryInsightCategory::where('slug', $data['slug'])->where('id', '!=', $industryInsightCategory->id)->exists()) {
            return response()->json(['error' => 'Category already exists.'], 422);
        }

        $industryInsightCategory->update($data);
        return response()->json(['category' => $industryInsightCategory, 'message' => 'Category updated.']);
    }

    public function categoryDestroy(IndustryInsightCategory $industryInsightCategory)
    {
        $industryInsightCategory->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
