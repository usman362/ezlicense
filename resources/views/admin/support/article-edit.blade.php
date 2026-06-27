@extends('layouts.admin')

@section('title', $article->exists ? 'Edit Article' : 'New Article')
@section('heading', 'Support › ' . ($article->exists ? 'Edit Article' : 'New Article'))

@section('content')
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ $article->exists ? route('admin.support.article.update', $article) : route('admin.support.article.store') }}">
    @csrf
    @if($article->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Title *</label><input name="title" class="form-control" required value="{{ old('title', $article->title) }}"></div>
                    <div class="mb-3"><label class="form-label">Slug (auto if blank)</label><input name="slug" class="form-control" value="{{ old('slug', $article->slug) }}"></div>
                    <div class="mb-3"><label class="form-label">Excerpt</label><textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $article->excerpt) }}</textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Content *</label>
                        <textarea name="content" id="article-content" rows="20" class="form-control">{{ old('content', $article->content) }}</textarea>
                    </div>
                    <div class="mb-3"><label class="form-label">Meta description (SEO)</label><textarea name="meta_description" rows="2" class="form-control" maxlength="500">{{ old('meta_description', $article->meta_description) }}</textarea></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Section *</label>
                        <select name="section_id" class="form-select" required>
                            <option value="">Choose…</option>
                            @foreach($sections as $s)<option value="{{ $s->id }}" {{ old('section_id', $article->section_id) == $s->id ? 'selected' : '' }}>{{ $s->category->name }} › {{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Sort order</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $article->sort_order ?? 0) }}"></div>
                    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_published" value="1" id="pub" {{ old('is_published', $article->is_published) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="pub">Published</label></div>

                    <button type="submit" class="btn btn-warning fw-bold w-100"><i class="bi bi-check-lg"></i> Save Article</button>
                    <a href="{{ route('admin.support.articles') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                </div>
            </div>

            @if($article->exists)
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body small">
                        <div><strong>{{ number_format($article->views_count) }}</strong> views</div>
                        <div>👍 {{ $article->helpful_yes_count }} · 👎 {{ $article->helpful_no_count }} ({{ $article->helpfulPercent() }}% helpful)</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</form>

@endsection

@push('scripts')
{{-- TinyMCE 7 (free, MIT) — load library then init --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#article-content',
    height: 540,
    menubar: 'edit insert format table',
    branding: false,
    promotion: false,
    plugins: 'lists link image table code autoresize searchreplace wordcount',
    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | alignleft aligncenter alignright | code removeformat',
    block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Preformatted=pre;Quote=blockquote',
    content_style: 'body { font-family: -apple-system, sans-serif; font-size: 14px; line-height: 1.6; }',
    relative_urls: false,
    remove_script_host: false,
    automatic_uploads: true,
    images_upload_handler: function (blobInfo, progress) {
        return new Promise((resolve, reject) => {
            const fd = new FormData();
            fd.append('file', blobInfo.blob(), blobInfo.filename());
            fetch('{{ route('admin.support.article.image-upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'Accept': 'application/json',
                },
                body: fd,
            })
            .then(r => r.ok ? r.json() : r.json().then(d => Promise.reject(d.error || 'Upload failed')))
            .then(data => data.location ? resolve(data.location) : reject('No URL returned'))
            .catch(err => reject(String(err)));
        });
    },
});

// Ensure TinyMCE writes back to the textarea before submit
document.querySelector('form').addEventListener('submit', () => tinymce.triggerSave());
</script>
@endpush
