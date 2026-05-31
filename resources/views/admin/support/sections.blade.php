@extends('layouts.admin')

@section('title', 'Support Sections')
@section('heading', 'Support › Sections')

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2">
        <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All categories</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
        </select>
    </form>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#newSecModal"><i class="bi bi-plus-lg"></i> New Section</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Category</th><th>Name</th><th>Slug</th><th>Articles</th><th>Status</th><th class="text-end pe-3">Actions</th></tr></thead>
            <tbody>
                @foreach($sections as $sec)
                    <tr>
                        <td class="ps-3"><span class="badge text-bg-light">{{ $sec->category->name }}</span></td>
                        <td><strong>{{ $sec->name }}</strong></td>
                        <td><code>{{ $sec->slug }}</code></td>
                        <td>{{ $sec->articles_count }}</td>
                        <td>@if($sec->is_active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Hidden</span>@endif</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary edit-sec" data-sec="{{ json_encode($sec) }}" data-bs-toggle="modal" data-bs-target="#editSecModal"><i class="bi bi-pencil"></i></button>
                            <form method="POST" action="{{ route('admin.support.section.destroy', $sec) }}" class="d-inline" onsubmit="return confirm('Delete section + its articles?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- New Modal --}}
<div class="modal fade" id="newSecModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('admin.support.section.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">New Section</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Category *</label><select name="category_id" class="form-select" required><option value="">Choose…</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="row g-2">
                <div class="col-7"><label class="form-label">Icon</label><input name="icon" class="form-control" placeholder="bi-info-circle"></div>
                <div class="col-5"><label class="form-label">Sort</label><input name="sort_order" type="number" class="form-control" value="0"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-warning fw-bold">Create</button></div>
    </form>
</div></div></div>

{{-- Edit Modal --}}
<div class="modal fade" id="editSecModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="editSecForm">@csrf @method('PUT')
        <div class="modal-header"><h5 class="modal-title">Edit Section</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Category *</label><select name="category_id" id="editSecCat" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" id="editSecName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editSecDesc" class="form-control" rows="2"></textarea></div>
            <div class="row g-2">
                <div class="col-6"><label class="form-label">Icon</label><input name="icon" id="editSecIcon" class="form-control"></div>
                <div class="col-3"><label class="form-label">Sort</label><input name="sort_order" id="editSecSort" type="number" class="form-control"></div>
                <div class="col-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="editSecActive"><label class="form-check-label" for="editSecActive">Active</label></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-warning fw-bold">Save</button></div>
    </form>
</div></div></div>

<div class="mt-3">{{ $sections->links() }}</div>

<script>
document.querySelectorAll('.edit-sec').forEach(btn => {
    btn.addEventListener('click', () => {
        const s = JSON.parse(btn.dataset.sec);
        document.getElementById('editSecForm').action = '/admin/support/sections/' + s.id;
        document.getElementById('editSecCat').value = s.category_id;
        document.getElementById('editSecName').value = s.name;
        document.getElementById('editSecDesc').value = s.description || '';
        document.getElementById('editSecIcon').value = s.icon || '';
        document.getElementById('editSecSort').value = s.sort_order || 0;
        document.getElementById('editSecActive').checked = !!s.is_active;
    });
});
</script>
@endsection
