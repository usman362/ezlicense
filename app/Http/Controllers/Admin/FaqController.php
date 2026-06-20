<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $q = Faq::query()->ordered();

        if ($search = trim((string) $request->query('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('question', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $faqs = $q->paginate(10)->withQueryString();

        return view('admin.pages.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.pages.faqs.form', ['faq' => new Faq(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('message', 'FAQ created.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.pages.faqs.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validateData($request, $faq));

        return redirect()->route('admin.faqs.index')->with('message', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('message', 'FAQ deleted.');
    }

    public function toggle(Faq $faq)
    {
        $faq->update(['is_published' => ! $faq->is_published]);

        return back()->with('message', 'FAQ ' . ($faq->is_published ? 'published' : 'unpublished') . '.');
    }

    private function validateData(Request $request, ?Faq $faq = null): array
    {
        $data = $request->validate([
            'question'    => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:191', 'alpha_dash',
                              'unique:faqs,slug' . ($faq ? ',' . $faq->id : '')],
            'category'    => ['nullable', 'string', 'max:100'],
            'answer'      => ['required', 'string'],
            'is_published'=> ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
