@extends('layouts.frontend')
@section('title', 'Frequently Asked Questions — Secure Licence')
@section('meta_description', 'New to Secure Licence or looking to learn to drive? Find answers to the questions our learners and instructors ask most.')

@push('styles')
<style>
    .faq-row{display:flex;align-items:center;gap:1rem;background:#fff;border:1px solid #eef0f2;border-radius:.75rem;padding:1.1rem 1.4rem;text-decoration:none;color:#1a1d21;transition:border-color .12s ease, box-shadow .12s ease;}
    .faq-row:hover{border-color:#ffd500;box-shadow:0 6px 20px rgba(20,23,28,.06);color:#1a1d21;}
    .faq-row .q{font-weight:600;flex:1;}
    .faq-row .chev{color:#adb5bd;}
    .faq-row:hover .chev{color:#caa300;}
    /* pagination — rounded square buttons */
    .faq-pagination{display:flex;justify-content:center;gap:.5rem;list-style:none;padding:0;margin:0;}
    .faq-pagination .page-item .page-link{display:flex;align-items:center;justify-content:center;width:40px;height:40px;border:1px solid #e6e9ed;border-radius:11px;background:#fff;color:#1a1d21;font-weight:700;font-size:.92rem;text-decoration:none;transition:all .12s ease;box-shadow:0 1px 2px rgba(20,23,28,.04);}
    .faq-pagination .page-item .page-link:hover{border-color:#ffd500;color:#1a1d21;}
    .faq-pagination .page-item.active .page-link{background:#ffd500;border-color:#ffd500;color:#1a1d21;box-shadow:0 6px 16px rgba(255,213,0,.35);}
    .faq-pagination .page-item.disabled .page-link{color:#cdd2d8;background:#fff;cursor:default;box-shadow:none;}
</style>
@endpush

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="py-5">
    <div class="container">
        <nav class="small text-muted mb-3">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            <span class="mx-1">/</span>
            <span class="text-dark">FAQs</span>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bolder mb-2">Frequently Asked Questions</h1>
                <p class="text-muted mb-0">New to Secure Licence or looking to learn to drive? Find answers to the questions our learners and instructors ask most.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('support.request.show') }}" class="btn btn-outline-dark">Contact our team</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── LIST ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="d-flex flex-column gap-3">
                    @foreach($faqs as $faq)
                        <a href="{{ route('faqs.show', $faq['slug']) }}" class="faq-row">
                            <span class="q">{{ $faq['question'] }}</span>
                            <i class="bi bi-chevron-right chev"></i>
                        </a>
                    @endforeach
                </div>

                @if($faqs->hasPages())
                    <nav class="mt-4" aria-label="FAQ pages">
                        <ul class="faq-pagination mb-3">
                            <li class="page-item {{ $faqs->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $faqs->previousPageUrl() ?: '#' }}" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            @for($p = 1; $p <= $faqs->lastPage(); $p++)
                                <li class="page-item {{ $p == $faqs->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $faqs->url($p) }}">{{ $p }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $faqs->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $faqs->nextPageUrl() ?: '#' }}" aria-label="Next"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                    <p class="text-center text-muted small">
                        Showing {{ $faqs->firstItem() }}–{{ $faqs->lastItem() }} of {{ $faqs->total() }} questions
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ─────────── STILL HAVE QUESTIONS ─────────── --}}
<section class="py-5 text-white" style="background:#14171c;">
    <div class="container text-center">
        <h2 class="fw-bolder mb-2 text-white">Still have questions?</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width:520px;">Our team is happy to help. Get in touch via our support centre and we'll get back to you.</p>
        <a href="{{ route('support.request.show') }}" class="btn btn-warning btn-lg fw-semibold">Contact our team</a>
    </div>
</section>

@endsection
