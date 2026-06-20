<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

/**
 * Public FAQ pages (data managed from Admin → FAQs).
 *   GET /faqs            → paginated list of questions
 *   GET /faqs/{slug}     → single question + answer
 */
class FaqController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        $faqs = Faq::published()->ordered()->paginate(self::PER_PAGE);

        return view('frontend.pages.faqs.index', compact('faqs'));
    }

    public function show(string $slug)
    {
        $faq = Faq::published()->where('slug', $slug)->firstOrFail();

        $related = Faq::published()->ordered()
            ->where('category', $faq->category)
            ->where('id', '!=', $faq->id)
            ->take(5)
            ->get();

        return view('frontend.pages.faqs.show', compact('faq', 'related'));
    }
}
