@extends('layouts.admin')
@section('title', 'Blog Posts')
@section('heading', 'Blog Posts')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="input-group" style="max-width:260px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="search-input" class="form-control" placeholder="Search posts...">
        </div>
        <select id="filter-status" class="form-select" style="max-width:160px;">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
        </select>
        <select id="filter-category" class="form-select" style="max-width:180px;">
            <option value="">All Categories</option>
        </select>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog.categories') }}" class="btn btn-outline-secondary"><i class="bi bi-tags me-1"></i>Categories</a>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Post</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px"></th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Published</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody id="posts-tbody">
                <tr><td colspan="8" class="text-center py-4 text-muted">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<nav class="mt-3 d-flex justify-content-between align-items-center">
    <small class="text-muted" id="pagination-info"></small>
    <div id="pagination-controls" class="d-flex gap-1"></div>
</nav>

{{-- Delete confirm modal --}}
<div class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Delete Post</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><p>Are you sure you want to delete "<span id="delete-post-title"></span>"? This cannot be undone.</p></div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-danger" id="confirm-delete-btn">Delete</button>
    </div>
</div></div></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    let currentPage = 1;
    let deleteId = null;

    // Load categories for filter
    fetch('/api/admin/blog/categories/list')
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('filter-category');
            (data.categories || []).forEach(c => {
                sel.innerHTML += `<option value="${c.id}">${c.name} (${c.posts_count})</option>`;
            });
        });

    function loadPosts(page) {
        currentPage = page || 1;
        const search = document.getElementById('search-input').value;
        const status = document.getElementById('filter-status').value;
        const catId = document.getElementById('filter-category').value;

        let url = `/api/admin/blog/list?page=${currentPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;
        if (catId) url += `&category_id=${catId}`;

        fetch(url).then(r => r.json()).then(data => {
            const tbody = document.getElementById('posts-tbody');
            if (!data.posts || data.posts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No posts found.</td></tr>';
                document.getElementById('pagination-info').textContent = '';
                document.getElementById('pagination-controls').innerHTML = '';
                return;
            }

            tbody.innerHTML = data.posts.map(p => `
                <tr>
                    <td class="text-center">
                        <button class="btn btn-sm p-0 border-0 toggle-featured" data-id="${p.id}" title="${p.is_featured ? 'Unfeature' : 'Feature'}">
                            <i class="bi ${p.is_featured ? 'bi-star-fill text-warning' : 'bi-star text-muted'}"></i>
                        </button>
                    </td>
                    <td>
                        <strong>${escHtml(p.title)}</strong>
                        <br><small class="text-muted">/blog/${p.slug}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">${p.category || '—'}</span></td>
                    <td class="small">${escHtml(p.author)}</td>
                    <td>
                        <span class="badge ${p.status === 'published' ? 'bg-success' : p.status === 'draft' ? 'bg-secondary' : 'bg-warning text-dark'}">
                            ${p.status}
                        </span>
                    </td>
                    <td class="small">${p.views.toLocaleString()}</td>
                    <td class="small">${p.published_at || '—'}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/admin/blog/${p.id}/edit" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${p.id}" data-title="${escAttr(p.title)}" title="Delete"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');

            // Pagination info
            document.getElementById('pagination-info').textContent = `Showing page ${data.current_page} of ${data.last_page} (${data.total} posts)`;

            // Pagination controls
            let pag = '';
            for (let i = 1; i <= data.last_page; i++) {
                pag += `<button class="btn btn-sm ${i === data.current_page ? 'btn-primary' : 'btn-outline-secondary'} page-btn" data-page="${i}">${i}</button>`;
            }
            document.getElementById('pagination-controls').innerHTML = pag;
        });
    }

    loadPosts(1);

    // Search and filter
    let searchTimer;
    document.getElementById('search-input').addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadPosts(1), 400);
    });
    document.getElementById('filter-status').addEventListener('change', () => loadPosts(1));
    document.getElementById('filter-category').addEventListener('change', () => loadPosts(1));

    // Pagination
    document.getElementById('pagination-controls').addEventListener('click', (e) => {
        const btn = e.target.closest('.page-btn');
        if (btn) loadPosts(parseInt(btn.dataset.page));
    });

    // Toggle featured
    document.getElementById('posts-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.toggle-featured');
        if (btn) {
            fetch(`/api/admin/blog/${btn.dataset.id}/toggle-featured`, {
                method: 'PATCH', headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
            }).then(r => r.json()).then(() => loadPosts(currentPage));
        }
    });

    // Delete
    document.getElementById('posts-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            deleteId = btn.dataset.id;
            document.getElementById('delete-post-title').textContent = btn.dataset.title;
            new bootstrap.Modal(document.getElementById('delete-modal')).show();
        }
    });
    document.getElementById('confirm-delete-btn').addEventListener('click', () => {
        if (!deleteId) return;
        fetch(`/api/admin/blog/${deleteId}`, {
            method: 'DELETE', headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
        }).then(r => r.json()).then(() => {
            bootstrap.Modal.getInstance(document.getElementById('delete-modal')).hide();
            loadPosts(currentPage);
            deleteId = null;
        });
    });

    function escHtml(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
    function escAttr(str) { return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
});
</script>
@endpush
