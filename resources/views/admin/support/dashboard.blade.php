@extends('layouts.admin')

@section('title', 'Support Dashboard')
@section('heading', 'Support Center')

@section('content')
<div class="row g-3 mb-4">
    @php
        $kpis = [
            ['Categories', $catCount, 'bi-folder2-open', 'primary'],
            ['Sections', $sectionCount, 'bi-folder', 'info'],
            ['Articles', $articleCount . ' (' . $publishedArticleCount . ' published)', 'bi-file-text', 'success'],
            ['New requests', $newRequestCount, 'bi-envelope-exclamation', 'warning'],
        ];
    @endphp
    @foreach($kpis as [$label, $val, $icon, $color])
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="text-muted small mb-0 text-uppercase" style="letter-spacing:.05em;">{{ $label }}</h6>
                        <i class="bi {{ $icon }} text-{{ $color }} fs-4"></i>
                    </div>
                    <div class="fs-3 fw-bold">{{ $val }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between">
                <strong>Manage Content</strong>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.support.categories') }}" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-folder2-open me-2"></i>Categories</a>
                <a href="{{ route('admin.support.sections') }}" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-folder me-2"></i>Sections</a>
                <a href="{{ route('admin.support.articles') }}" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-file-text me-2"></i>Articles</a>
                <a href="{{ route('admin.support.requests') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-envelope me-2"></i>Requests Inbox</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>Most-viewed Articles</strong></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topArticles as $a)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-truncate" style="max-width:60%">{{ $a->title }}</span>
                            <span class="text-muted small"><i class="bi bi-eye"></i> {{ number_format($a->views_count) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No views yet</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>Recent Requests</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light"><tr><th class="ps-3">Ref</th><th>From</th><th>Subject</th><th>Status</th><th>When</th><th></th></tr></thead>
            <tbody>
                @forelse($recentRequests as $r)
                    <tr>
                        <td class="ps-3"><code>{{ $r->reference }}</code></td>
                        <td>{{ $r->name }}<br><span class="small text-muted">{{ $r->email }}</span></td>
                        <td class="text-truncate" style="max-width:300px;">{{ $r->subject }}</td>
                        <td><span class="badge text-bg-{{ $r->statusBadge() }}">{{ ucfirst($r->status) }}</span></td>
                        <td><span class="small text-muted">{{ $r->created_at->diffForHumans() }}</span></td>
                        <td><a href="{{ route('admin.support.request.show', $r) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No requests yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
