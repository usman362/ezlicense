@extends('layouts.frontend')
@section('title', ($policyTitle ?? 'Policy') . ' — Secure Licences')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('policies.index') }}">Policies</a></li>
                <li class="breadcrumb-item active">{{ $policyTitle ?? 'Policy' }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            {{-- Left sidebar: all policies navigation --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm sticky-lg-top" style="top:20px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Policies</h6>
                    </div>
                    <div class="list-group list-group-flush small">
                        @php
                            $policyLinks = [
                                'policies.index'                  => ['label' => 'All Policies', 'icon' => 'grid'],
                                'policies.instructor-conduct'     => ['label' => 'Instructor Code of Conduct', 'icon' => 'person-badge'],
                                'policies.learner-conduct'        => ['label' => 'Learner Code of Conduct', 'icon' => 'person'],
                                'policies.complaint-handling'     => ['label' => 'Complaint Handling', 'icon' => 'exclamation-octagon'],
                                'policies.refund-cancellation'    => ['label' => 'Refund & Cancellation', 'icon' => 'arrow-counterclockwise'],
                                'policies.safety'                 => ['label' => 'Safety Policy', 'icon' => 'shield-shaded'],
                                'policies.dispute-resolution'     => ['label' => 'Dispute Resolution', 'icon' => 'people'],
                                'terms'                           => ['label' => 'Terms & Conditions', 'icon' => 'file-earmark-text'],
                                'privacy'                         => ['label' => 'Privacy Policy', 'icon' => 'lock'],
                            ];
                        @endphp
                        @foreach($policyLinks as $routeName => $meta)
                            <a href="{{ route($routeName) }}"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs($routeName) ? 'active' : '' }}">
                                <i class="bi bi-{{ $meta['icon'] }}"></i>{{ $meta['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Main content --}}
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-2" style="color: var(--ez-dark);">{{ $policyTitle ?? 'Policy' }}</h1>
                @if(!empty($policyLead))
                    <p class="lead text-muted mb-3">{{ $policyLead }}</p>
                @endif
                <p class="text-muted small mb-4">
                    <i class="bi bi-calendar3 me-1"></i>Last updated: {{ $policyUpdated ?? date('F Y') }}
                    @if(!empty($policyVersion))
                        &middot; Version {{ $policyVersion }}
                    @endif
                </p>
                <hr class="mb-4">

                @yield('policy-body')

                <hr class="my-5">
                <div class="alert alert-light border">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="bi bi-info-circle fs-4 text-primary"></i>
                        <div class="small mb-0">
                            Have a question about this policy or need to report something?
                            <a href="{{ route('contact') }}" class="fw-semibold">Contact our support team</a> —
                            we respond to all policy enquiries within two business days.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
