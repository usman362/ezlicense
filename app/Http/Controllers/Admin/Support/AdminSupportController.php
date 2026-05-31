<?php

namespace App\Http\Controllers\Admin\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportArticle;
use App\Models\SupportCategory;
use App\Models\SupportRequest;
use App\Models\SupportSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSupportController extends Controller
{
    /* ──────────────── DASHBOARD ──────────────── */

    public function dashboard()
    {
        return view('admin.support.dashboard', [
            'catCount'     => SupportCategory::count(),
            'sectionCount' => SupportSection::count(),
            'articleCount' => SupportArticle::count(),
            'publishedArticleCount' => SupportArticle::where('is_published', true)->count(),
            'requestCount' => SupportRequest::count(),
            'newRequestCount' => SupportRequest::where('status', SupportRequest::STATUS_NEW)->count(),
            'topArticles'  => SupportArticle::orderByDesc('views_count')->limit(5)->get(),
            'recentRequests' => SupportRequest::latest()->limit(5)->get(),
        ]);
    }

    /* ──────────────── CATEGORIES ──────────────── */

    public function categoriesIndex()
    {
        $categories = SupportCategory::orderBy('sort_order')->withCount('sections')->paginate(20);
        return view('admin.support.categories', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:60',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['is_active'] = true;
        SupportCategory::create($data);
        return back()->with('message', 'Category created.');
    }

    public function categoryUpdate(Request $request, SupportCategory $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:60',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);
        return back()->with('message', 'Category updated.');
    }

    public function categoryDestroy(SupportCategory $category)
    {
        $category->delete();
        return back()->with('message', 'Category deleted.');
    }

    /* ──────────────── SECTIONS ──────────────── */

    public function sectionsIndex(Request $request)
    {
        $query = SupportSection::with('category')->orderBy('category_id')->orderBy('sort_order');
        if ($cid = $request->input('category_id')) {
            $query->where('category_id', $cid);
        }
        $sections = $query->withCount('articles')->paginate(30)->withQueryString();
        $categories = SupportCategory::orderBy('sort_order')->get();
        return view('admin.support.sections', compact('sections', 'categories'));
    }

    public function sectionStore(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:60',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['is_active'] = true;
        SupportSection::create($data);
        return back()->with('message', 'Section created.');
    }

    public function sectionUpdate(Request $request, SupportSection $section)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:60',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $section->update($data);
        return back()->with('message', 'Section updated.');
    }

    public function sectionDestroy(SupportSection $section)
    {
        $section->delete();
        return back()->with('message', 'Section deleted.');
    }

    /* ──────────────── ARTICLES ──────────────── */

    public function articlesIndex(Request $request)
    {
        $query = SupportArticle::with('section.category');
        if ($sid = $request->input('section_id')) $query->where('section_id', $sid);
        if ($pub = $request->input('published')) {
            $query->where('is_published', $pub === 'yes');
        }
        if ($q = $request->input('q')) {
            $query->where('title', 'like', "%{$q}%");
        }
        $articles = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $sections = SupportSection::with('category')->orderBy('category_id')->orderBy('sort_order')->get();
        return view('admin.support.articles', compact('articles', 'sections'));
    }

    public function articleCreate()
    {
        $sections = SupportSection::with('category')->orderBy('category_id')->orderBy('sort_order')->get();
        return view('admin.support.article-edit', ['article' => new SupportArticle(), 'sections' => $sections]);
    }

    public function articleEdit(SupportArticle $article)
    {
        $sections = SupportSection::with('category')->orderBy('category_id')->orderBy('sort_order')->get();
        return view('admin.support.article-edit', compact('article', 'sections'));
    }

    public function articleStore(Request $request)
    {
        $data = $this->validateArticle($request);
        $data['author_id'] = Auth::id();
        SupportArticle::create($data);
        return redirect()->route('admin.support.articles')->with('message', 'Article created.');
    }

    public function articleUpdate(Request $request, SupportArticle $article)
    {
        $data = $this->validateArticle($request);
        $article->update($data);
        return redirect()->route('admin.support.articles')->with('message', 'Article updated.');
    }

    public function articleDestroy(SupportArticle $article)
    {
        $article->delete();
        return back()->with('message', 'Article deleted.');
    }

    private function validateArticle(Request $request): array
    {
        $data = $request->validate([
            'section_id'       => 'required|exists:support_sections,id',
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:280',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'sort_order'       => 'nullable|integer',
            'is_published'     => 'nullable|boolean',
        ]);
        $data['is_published'] = $request->boolean('is_published');
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']);
        return $data;
    }

    /**
     * Image upload endpoint for TinyMCE in the article editor.
     * Saves to DigitalOcean Spaces under `support-articles/{Y}/{m}/uuid.ext`
     * and returns { location: <public-url> } as TinyMCE expects.
     */
    public function articleImageUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:8192'],
        ]);

        try {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
            $path = 'support-articles/' . date('Y/m') . '/' . Str::uuid() . '.' . $ext;
            Storage::disk('spaces')->put($path, file_get_contents($file->getRealPath()), 'public');
            $url = Storage::disk('spaces')->url($path);
            return response()->json(['location' => $url]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    /* ──────────────── REQUESTS (Inbox) ──────────────── */

    public function requestsIndex(Request $request)
    {
        $query = SupportRequest::query();
        if ($status = $request->input('status')) $query->where('status', $status);
        if ($q = $request->input('q')) {
            $query->where(function ($qq) use ($q) {
                $qq->where('reference', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }
        $requests = $query->orderByDesc('id')->paginate(30)->withQueryString();

        $counts = [
            'all'      => SupportRequest::count(),
            'new'      => SupportRequest::where('status', SupportRequest::STATUS_NEW)->count(),
            'open'     => SupportRequest::where('status', SupportRequest::STATUS_OPEN)->count(),
            'pending'  => SupportRequest::where('status', SupportRequest::STATUS_PENDING)->count(),
            'resolved' => SupportRequest::where('status', SupportRequest::STATUS_RESOLVED)->count(),
        ];

        return view('admin.support.requests', compact('requests', 'counts'));
    }

    public function requestShow(SupportRequest $request)
    {
        return view('admin.support.request-detail', ['req' => $request]);
    }

    public function requestUpdate(Request $request, SupportRequest $supportRequest)
    {
        $data = $request->validate([
            'status'       => 'nullable|in:new,open,pending,resolved,closed',
            'admin_notes'  => 'nullable|string|max:2000',
            'response'     => 'nullable|string|max:5000',
        ]);

        if (! empty($data['response']) && empty($supportRequest->responded_at)) {
            $data['responded_at'] = now();
            $data['responded_by'] = Auth::id();
        }
        $supportRequest->update($data);
        return back()->with('message', 'Request updated.');
    }
}
