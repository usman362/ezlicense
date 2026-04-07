@csrf
<div class="mb-3">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" required value="{{ old('name', $category->name ?? '') }}" class="form-control">
</div>
<div class="mb-3">
    <label class="form-label">Slug <small class="text-muted">(auto if blank)</small></label>
    <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="form-control">
</div>
<div class="mb-3">
    <label class="form-label">Icon <small class="text-muted">(bootstrap-icon name e.g. <code>wrench</code>)</small></label>
    <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" class="form-control" placeholder="wrench">
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Commission rate (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="commission_rate" required value="{{ old('commission_rate', $category->commission_rate ?? 10) }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Display order</label>
        <input type="number" name="display_order" value="{{ old('display_order', $category->display_order ?? 0) }}" class="form-control">
    </div>
</div>
<div class="form-check mb-4">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $category->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>
<div class="d-flex gap-2">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('admin.service-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
