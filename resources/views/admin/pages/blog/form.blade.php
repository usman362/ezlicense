@extends('layouts.admin')
@section('title', $post ? 'Edit Post' : 'New Post')
@section('heading', $post ? 'Edit Post' : 'New Blog Post')

@section('content')
<form action="{{ $post ? route('admin.blog.update', $post) : route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" id="blog-form">
    @csrf
    @if($post) @method('PUT') @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Main content --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $post?->title) }}" required id="title-input">
                        <small class="text-muted" id="slug-preview">{{ $post ? 'Slug: /blog/' . $post->slug : '' }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="2" maxlength="500" placeholder="Short summary for listing cards (max 500 chars)">{{ old('excerpt', $post?->excerpt) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <div class="mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="wrapSelection('**', '**')" title="Bold"><i class="bi bi-type-bold"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="wrapSelection('*', '*')" title="Italic"><i class="bi bi-type-italic"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertText('\n## ')" title="Heading"><i class="bi bi-type-h2"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertText('\n- ')" title="List"><i class="bi bi-list-ul"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="wrapSelection('[', '](url)')" title="Link"><i class="bi bi-link-45deg"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertText('\n![alt](image-url)\n')" title="Image"><i class="bi bi-image"></i></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="wrapSelection('\n```\n', '\n```\n')" title="Code Block"><i class="bi bi-code-slash"></i></button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="togglePreview()" id="preview-toggle-btn"><i class="bi bi-eye me-1"></i>Preview</button>
                        </div>
                        <textarea name="body" id="body-editor" class="form-control font-monospace" rows="18" required style="font-size:0.9rem;">{{ old('body', $post?->body) }}</textarea>
                        <div id="body-preview" class="border rounded p-3 bg-white d-none" style="min-height:200px;"></div>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-search me-1"></i> SEO Settings</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" maxlength="255" value="{{ old('meta_title', $post?->meta_title) }}" placeholder="Leave blank to use post title">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" maxlength="255" placeholder="Leave blank to use excerpt">{{ old('meta_description', $post?->meta_description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Publish settings --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Publish</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status', $post?->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post?->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $post?->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}">
                        <small class="text-muted">Leave blank to publish immediately when status is Published</small>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is-featured" {{ old('is_featured', $post?->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is-featured">Featured Post</label>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary flex-fill">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-check-lg me-1"></i>{{ $post ? 'Update' : 'Create' }}</button>
                </div>
            </div>

            {{-- Category --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Category</h6></div>
                <div class="card-body">
                    <select name="category_id" class="form-select" id="category-select">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $post?->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2">
                        <div class="input-group input-group-sm">
                            <input type="text" id="new-cat-input" class="form-control" placeholder="New category name...">
                            <button type="button" class="btn btn-outline-primary" id="add-cat-btn"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold">Featured Image</h6></div>
                <div class="card-body">
                    @if($post?->featured_image)
                        <div class="mb-2">
                            <img src="{{ $post->getImageUrl() }}" class="img-fluid rounded" alt="Current image" id="current-image">
                        </div>
                    @endif
                    <input type="file" name="featured_image" class="form-control" accept="image/*" id="image-input">
                    <div id="image-preview" class="mt-2 d-none">
                        <img src="" class="img-fluid rounded" id="preview-img">
                    </div>
                    <small class="text-muted">Max 5MB. Recommended: 1200x630px</small>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Slug preview
    const titleInput = document.getElementById('title-input');
    const slugPreview = document.getElementById('slug-preview');
    @if(!$post)
    titleInput.addEventListener('input', () => {
        const slug = titleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        slugPreview.textContent = slug ? 'Slug: /blog/' + slug : '';
    });
    @endif

    // Image preview
    document.getElementById('image-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const prev = document.getElementById('image-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                document.getElementById('preview-img').src = ev.target.result;
                prev.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            prev.classList.add('d-none');
        }
    });

    // Add category inline
    document.getElementById('add-cat-btn').addEventListener('click', () => {
        const input = document.getElementById('new-cat-input');
        const name = input.value.trim();
        if (!name) return;
        fetch('/api/admin/blog/categories', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
            body: JSON.stringify({name})
        }).then(r => r.json()).then(data => {
            if (data.error) { alert(data.error); return; }
            const sel = document.getElementById('category-select');
            const opt = document.createElement('option');
            opt.value = data.category.id;
            opt.textContent = data.category.name;
            opt.selected = true;
            sel.appendChild(opt);
            input.value = '';
        });
    });

    // Simple Markdown preview
    window.togglePreview = function() {
        const editor = document.getElementById('body-editor');
        const preview = document.getElementById('body-preview');
        const btn = document.getElementById('preview-toggle-btn');
        if (preview.classList.contains('d-none')) {
            preview.innerHTML = simpleMarkdown(editor.value);
            preview.classList.remove('d-none');
            editor.classList.add('d-none');
            btn.innerHTML = '<i class="bi bi-pencil me-1"></i>Edit';
        } else {
            preview.classList.add('d-none');
            editor.classList.remove('d-none');
            btn.innerHTML = '<i class="bi bi-eye me-1"></i>Preview';
        }
    };

    // Editor helpers
    window.wrapSelection = function(before, after) {
        const ta = document.getElementById('body-editor');
        const start = ta.selectionStart, end = ta.selectionEnd;
        const sel = ta.value.substring(start, end) || 'text';
        ta.value = ta.value.substring(0, start) + before + sel + after + ta.value.substring(end);
        ta.focus();
        ta.selectionStart = start + before.length;
        ta.selectionEnd = start + before.length + sel.length;
    };
    window.insertText = function(text) {
        const ta = document.getElementById('body-editor');
        const pos = ta.selectionStart;
        ta.value = ta.value.substring(0, pos) + text + ta.value.substring(pos);
        ta.focus();
        ta.selectionStart = ta.selectionEnd = pos + text.length;
    };

    function simpleMarkdown(md) {
        return md
            .replace(/^### (.+)$/gm, '<h3>$1</h3>')
            .replace(/^## (.+)$/gm, '<h2>$1</h2>')
            .replace(/^# (.+)$/gm, '<h1>$1</h1>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/!\[(.+?)\]\((.+?)\)/g, '<img src="$2" alt="$1" class="img-fluid">')
            .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2">$1</a>')
            .replace(/^- (.+)$/gm, '<li>$1</li>')
            .replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>')
            .replace(/```([\s\S]*?)```/g, '<pre class="bg-light p-2 rounded"><code>$1</code></pre>')
            .replace(/\n\n/g, '</p><p>')
            .replace(/^\s*/, '<p>')
            .replace(/\s*$/, '</p>');
    }
});
</script>
@endpush
