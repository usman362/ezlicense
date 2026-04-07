@extends('layouts.frontend')

@section('title', $category->name . 's')

@section('content')
<div class="container py-5">
    <a href="{{ route('services.categories') }}" class="text-decoration-none small">&larr; All categories</a>
    <h1 class="h2 fw-bold mt-2 mb-1">{{ $category->name }}s</h1>
    <p class="text-muted mb-4">{{ $category->description }}</p>

    <form method="GET" class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-2">
            <input type="text" name="suburb" placeholder="Suburb" value="{{ request('suburb') }}" class="form-control" style="max-width:200px;">
            <input type="text" name="postcode" placeholder="Postcode" value="{{ request('postcode') }}" class="form-control" style="max-width:140px;">
            <select name="sort" class="form-select" style="max-width:220px;">
                <option value="">Sort by</option>
                <option value="price_asc" @selected(request('sort')==='price_asc')>Price: Low to High</option>
                <option value="price_desc" @selected(request('sort')==='price_desc')>Price: High to Low</option>
                <option value="experience" @selected(request('sort')==='experience')>Most Experienced</option>
            </select>
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="row g-3">
        @forelse($providers as $provider)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 fw-semibold mb-1">{{ $provider->business_name ?: $provider->user->name }}</h3>
                        <p class="text-muted small mb-2">{{ $provider->base_suburb }} {{ $provider->base_postcode }}</p>
                        <p class="text-muted small mb-3" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">{{ $provider->service_description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold fs-5">${{ number_format($provider->hourly_rate, 2) }}/hr</span>
                            <a href="{{ route('services.show', [$category->slug, $provider]) }}" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">No providers found in this category yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $providers->links() }}</div>
</div>
@endsection
