@extends('layouts.admin')
@section('title', 'Blog Categories')
@section('heading', 'Blog Categories')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Add New Category</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" id="cat-name" class="form-control" placeholder="e.g. Driving Tips">
                </div>
                <button class="btn btn-primary" id="save-cat-btn"><i class="bi bi-plus-lg me-1"></i>Add Category</button>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Categories</h6>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Posts</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Slug</th><th>Posts</th><th style="width:130px">Actions</th></tr>
                    </thead>
                    <tbody id="cat-tbody">
                        <tr><td colspan="4" class="text-center py-3 text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div class="modal fade" id="edit-modal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Edit Category</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" id="edit-cat-id">
        <label class="form-label">Name</label>
        <input type="text" id="edit-cat-name" class="form-control">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-primary" id="update-cat-btn">Update</button>
    </div>
</div></div></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const headers = {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'};

    function loadCategories() {
        fetch('/api/admin/blog/categories/list').then(r => r.json()).then(data => {
            const tbody = document.getElementById('cat-tbody');
            if (!data.categories || data.categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No categories yet.</td></tr>';
                return;
            }
            tbody.innerHTML = data.categories.map(c => `
                <tr>
                    <td><strong>${escHtml(c.name)}</strong></td>
                    <td class="small text-muted">${c.slug}</td>
                    <td><span class="badge bg-light text-dark border">${c.posts_count}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${c.id}" data-name="${escAttr(c.name)}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${c.id}" data-name="${escAttr(c.name)}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        });
    }
    loadCategories();

    // Add
    document.getElementById('save-cat-btn').addEventListener('click', () => {
        const name = document.getElementById('cat-name').value.trim();
        if (!name) return;
        fetch('/api/admin/blog/categories', {
            method: 'POST', headers, body: JSON.stringify({name})
        }).then(r => r.json()).then(data => {
            if (data.error) { alert(data.error); return; }
            document.getElementById('cat-name').value = '';
            loadCategories();
        });
    });

    // Edit
    document.getElementById('cat-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.edit-btn');
        if (btn) {
            document.getElementById('edit-cat-id').value = btn.dataset.id;
            document.getElementById('edit-cat-name').value = btn.dataset.name;
            new bootstrap.Modal(document.getElementById('edit-modal')).show();
        }
    });
    document.getElementById('update-cat-btn').addEventListener('click', () => {
        const id = document.getElementById('edit-cat-id').value;
        const name = document.getElementById('edit-cat-name').value.trim();
        if (!name) return;
        fetch(`/api/admin/blog/categories/${id}`, {
            method: 'PUT', headers, body: JSON.stringify({name})
        }).then(r => r.json()).then(data => {
            if (data.error) { alert(data.error); return; }
            bootstrap.Modal.getInstance(document.getElementById('edit-modal')).hide();
            loadCategories();
        });
    });

    // Delete
    document.getElementById('cat-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.delete-btn');
        if (btn && confirm(`Delete category "${btn.dataset.name}"?`)) {
            fetch(`/api/admin/blog/categories/${btn.dataset.id}`, {
                method: 'DELETE', headers
            }).then(r => r.json()).then(() => loadCategories());
        }
    });

    function escHtml(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
    function escAttr(str) { return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
});
</script>
@endpush
