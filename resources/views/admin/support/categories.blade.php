@extends('layouts.admin')

@section('title', 'Support Categories')
@section('heading', 'Support › Categories')

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">{{ $categories->total() }} categories</h5>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#newCatModal">
        <i class="bi bi-plus-lg"></i> New Category
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Name</th><th>Slug</th><th>Icon</th><th>Sort</th><th>Sections</th><th>Status</th><th class="text-end pe-3">Actions</th></tr></thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td class="ps-3"><strong>{{ $cat->name }}</strong>@if($cat->description)<br><span class="small text-muted">{{ Str::limit($cat->description, 80) }}</span>@endif</td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td>@if($cat->icon)<i class="bi {{ $cat->icon }}"></i> <code class="small">{{ $cat->icon }}</code>@endif</td>
                        <td>{{ $cat->sort_order }}</td>
                        <td>{{ $cat->sections_count }}</td>
                        <td>@if($cat->is_active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Hidden</span>@endif</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary edit-cat" data-cat="{{ json_encode($cat) }}" data-bs-toggle="modal" data-bs-target="#editCatModal"><i class="bi bi-pencil"></i></button>
                            <form method="POST" action="{{ route('admin.support.category.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('Delete this category and ALL its sections + articles?')">
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
<div class="modal fade" id="newCatModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('admin.support.category.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">New Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="row g-2">
                <div class="col-7"><label class="form-label">Icon (bootstrap-icons class)</label><input name="icon" class="form-control" placeholder="bi-mortarboard-fill"></div>
                <div class="col-5"><label class="form-label">Sort order</label><input name="sort_order" type="number" class="form-control" value="0"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-warning fw-bold">Create</button></div>
    </form>
</div></div></div>

{{-- Edit Modal --}}
<div class="modal fade" id="editCatModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="editCatForm">@csrf @method('PUT')
        <div class="modal-header"><h5 class="modal-title">Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name *</label><input name="name" id="editCatName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editCatDesc" class="form-control" rows="2"></textarea></div>
            <div class="row g-2">
                <div class="col-6"><label class="form-label">Icon</label><input name="icon" id="editCatIcon" class="form-control"></div>
                <div class="col-3"><label class="form-label">Sort</label><input name="sort_order" id="editCatSort" type="number" class="form-control"></div>
                <div class="col-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="editCatActive"><label class="form-check-label" for="editCatActive">Active</label></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-warning fw-bold">Save</button></div>
    </form>
</div></div></div>

<div class="mt-3">{{ $categories->links() }}</div>

<script>
document.querySelectorAll('.edit-cat').forEach(btn => {
    btn.addEventListener('click', () => {
        const c = JSON.parse(btn.dataset.cat);
        document.getElementById('editCatForm').action = '/admin/support/categories/' + c.id;
        document.getElementById('editCatName').value = c.name || '';
        document.getElementById('editCatDesc').value = c.description || '';
        document.getElementById('editCatIcon').value = c.icon || '';
        document.getElementById('editCatSort').value = c.sort_order || 0;
        document.getElementById('editCatActive').checked = !!c.is_active;
    });
});
</script>
@endsection
