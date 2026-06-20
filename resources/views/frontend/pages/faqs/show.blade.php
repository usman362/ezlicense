@extends('layouts.frontend')
@section('title', $faq->question . ' — FAQs — Secure Licence')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($faq->answer), 155))

@push('styles')
<style>
    .faq-answer{color:#3c4149;line-height:1.7;}
    .faq-answer a{color:#caa300;text-decoration:underline;}
    .faq-answer h2,.faq-answer h3,.faq-answer h4{font-weight:700;margin-top:1.5rem;}
    .faq-answer ul{padding-left:1.2rem;}
    .faq-answer li{margin-bottom:.35rem;}
    .faq-search-wrap{background:#f8f9fb;}
    .faq-search-card{background:#fff;border:1px solid #eef0f2;border-radius:1rem;box-shadow:0 10px 34px rgba(20,23,28,.06);}
</style>
@endpush

@section('content')

{{-- ─────────── ANSWER ─────────── --}}
<section class="py-5">
    <div class="container">
        <nav class="small text-muted mb-3">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('faqs.index') }}" class="text-muted text-decoration-none">FAQs</a>
            <span class="mx-1">/</span>
            <span class="text-dark">{{ \Illuminate\Support\Str::limit($faq->question, 45) }}</span>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-bolder mb-2 mt-4">{{ $faq->question }}</h1>
                <p class="text-muted small mb-4">Updated {{ $faq->updated_at->diffForHumans() }}</p>

                <div class="fs-6 faq-answer">
                    {!! $faq->answer !!}
                </div>

                @if($related->isNotEmpty())
                    <div class="mt-5 pt-4 border-top">
                        <h2 class="h6 fw-bold text-uppercase text-muted mb-3" style="letter-spacing:.05em;">Related questions</h2>
                        <div class="d-flex flex-column gap-2">
                            @foreach($related as $r)
                                <a href="{{ route('faqs.show', $r->slug) }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset p-2 rounded" style="background:#f8f9fa;">
                                    <i class="bi bi-question-circle text-warning"></i>
                                    <span>{{ $r->question }}</span>
                                    <i class="bi bi-chevron-right text-muted ms-auto"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ─────────── FIND AN INSTRUCTOR ─────────── --}}
<section class="py-5 faq-search-wrap">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge text-bg-warning mb-3">Find Your Instructor</span>
            <h2 class="fw-bolder">Search for an instructor and book your lessons</h2>
            <p class="text-muted mb-0">Compare verified instructors in your suburb. Bundle lessons to unlock a bigger discount.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form method="get" action="{{ route('find-instructor.results') }}" class="faq-search-card p-3 p-md-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Pick-up location <span class="text-danger">*</span></label>
                            <input type="text" name="q" class="form-control" placeholder="Enter your suburb" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Transmission</label>
                            <select name="transmission" class="form-select">
                                <option value="">Any</option>
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-warning w-100 fw-semibold"><i class="bi bi-search me-1"></i>Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── STILL NEED HELP ─────────── --}}
<section class="py-5 text-white" style="background:#14171c;">
    <div class="container text-center">
        <h2 class="fw-bolder mb-2 text-white">Still need help?</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width:520px;">Browse all FAQs or get in touch with our team — we'll get back to you quickly.</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('faqs.index') }}" class="btn btn-warning btn-lg fw-semibold">Back to all FAQs</a>
            <a href="{{ route('support.request.show') }}" class="btn btn-outline-light btn-lg">Contact our team</a>
        </div>
    </div>
</section>

@endsection
