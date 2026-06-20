@extends('layouts.admin')

@section('title', $faq->exists ? 'Edit FAQ' : 'New FAQ')
@section('heading', $faq->exists ? 'Edit FAQ' : 'New FAQ')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="post" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
    @csrf
    @if($faq->exists) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" required maxlength="255"
                               value="{{ old('question', $faq->question) }}" placeholder="e.g. How much do driving lessons cost?">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Answer <span class="text-danger">*</span></label>
                        <textarea name="answer" id="faq-answer" rows="14">{{ old('answer', $faq->answer) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Settings</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category</label>
                        <input type="text" name="category" class="form-control" list="faq-cats" maxlength="100"
                               value="{{ old('category', $faq->category) }}" placeholder="e.g. Lessons & Pricing">
                        <datalist id="faq-cats">
                            @foreach(['Lessons & Pricing','Booking & Account','Payments','Getting Started','For Instructors'] as $c)
                                <option value="{{ $c }}">
                            @endforeach
                        </datalist>
                        <small class="text-muted">Groups related questions together.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sort order</label>
                        <input type="number" name="sort_order" class="form-control" min="0" max="9999"
                               value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
                        <small class="text-muted">Lower numbers appear first.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Slug (optional)</label>
                        <input type="text" name="slug" class="form-control" maxlength="191"
                               value="{{ old('slug', $faq->slug) }}" placeholder="auto-generated from question">
                        <small class="text-muted">Leave blank to auto-generate.</small>
                    </div>

                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_published" id="is_published" value="1"
                               @checked(old('is_published', $faq->is_published ?? true))>
                        <label class="form-check-label" for="is_published">Published (visible on site)</label>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">{{ $faq->exists ? 'Save changes' : 'Create FAQ' }}</button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#faq-answer',
    height: 420,
    menubar: 'edit insert format table',
    branding: false,
    promotion: false,
    plugins: 'lists link table code autoresize searchreplace wordcount',
    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link table | alignleft aligncenter alignright | code removeformat',
    block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Quote=blockquote',
    content_style: 'body { font-family: -apple-system, sans-serif; font-size: 14px; line-height: 1.6; }',
    relative_urls: false,
    remove_script_host: false,
});
document.querySelector('form').addEventListener('submit', () => tinymce.triggerSave());
</script>
@endpush
