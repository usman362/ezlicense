@extends('layouts.admin')

@section('title', $question->exists ? 'Edit Question' : 'New Question')
@section('heading', $question->exists ? 'Edit Practice Question' : 'New Practice Question')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

@php $opts = old('options', $question->options ?: ['', '', '', '']); $correct = (int) old('correct_index', $question->correct_index ?? 0); @endphp

<form method="post" action="{{ $question->exists ? route('admin.practice-questions.update', $question) : route('admin.practice-questions.store') }}" enctype="multipart/form-data">
    @csrf
    @if($question->exists) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <textarea name="question" class="form-control" rows="3" required maxlength="1000">{{ old('question', $question->question) }}</textarea>
                    </div>

                    <label class="form-label fw-semibold">Answer options <span class="text-danger">*</span></label>
                    <p class="small text-muted mb-2">Tick the radio next to the correct answer. Leave unused options blank.</p>
                    @for($i = 0; $i < 5; $i++)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct_index" value="{{ $i }}" @checked($correct === $i) aria-label="Correct answer">
                            </div>
                            <input type="text" name="options[{{ $i }}]" class="form-control" maxlength="255"
                                   placeholder="Option {{ $i + 1 }}{{ $i >= 2 ? ' (optional)' : '' }}"
                                   value="{{ $opts[$i] ?? '' }}">
                        </div>
                    @endfor

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Explanation (shown in review)</label>
                        <textarea name="explanation" class="form-control" rows="2" maxlength="1000">{{ old('explanation', $question->explanation) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Settings</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">State</label>
                        <select name="state" class="form-select">
                            <option value="" @selected(old('state', $question->state)===null || old('state', $question->state)==='')>All states (common)</option>
                            @foreach(\App\Models\PracticeQuestion::STATES as $slug => $name)
                                <option value="{{ $slug }}" @selected(old('state', $question->state)===$slug)>{{ $name }} ({{ strtoupper($slug) }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block">Pick a state to show this question only in that state's test. "All states" shows it in every state's test.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Section</label>
                        <select name="section" class="form-select">
                            <option value="general" @selected(old('section', $question->section)==='general')>General Knowledge</option>
                            <option value="road_safety" @selected(old('section', $question->section)==='road_safety')>Road Safety</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Question image (optional)</label>
                        @if($question->image_path)
                            <div class="mb-2">
                                <img src="{{ $question->image_url }}" alt="" class="img-fluid rounded border" style="max-height:140px;">
                                <div class="form-check mt-1">
                                    <input type="checkbox" class="form-check-input" name="remove_image" id="remove_image" value="1">
                                    <label for="remove_image" class="form-check-label small">Remove current image</label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block">
                            <strong>Recommended:</strong> landscape, around <strong>800 × 500 px</strong> (4:3 or 16:9). JPG/PNG, max 4 MB.
                            Any size works — it's auto-fitted to max 300px tall, centered, without stretching.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sort order</label>
                        <input type="number" name="sort_order" class="form-control" min="0" max="9999" value="{{ old('sort_order', $question->sort_order ?? 0) }}">
                    </div>

                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" @checked(old('is_active', $question->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active (used in tests)</label>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">{{ $question->exists ? 'Save changes' : 'Add question' }}</button>
                <a href="{{ route('admin.practice-questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
