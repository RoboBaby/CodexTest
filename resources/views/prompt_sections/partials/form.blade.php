<div class="mb-3">
    <label class="form-label">Key</label>
    <input class="form-control" name="key" value="{{ old('key', $section->key ?? '') }}">
    @error('key')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Title</label>
    <input class="form-control" name="title" value="{{ old('title', $section->title ?? '') }}">
    @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="3">{{ old('description', $section->description ?? '') }}</textarea>
    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Order Index</label>
        <input type="number" class="form-control" name="order_index" value="{{ old('order_index', $section->order_index ?? 0) }}">
        @error('order_index')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Enabled</label>
        <select class="form-select" name="enabled">
            <option value="1" @selected(old('enabled', $section->enabled ?? true))>Enabled</option>
            <option value="0" @selected(!old('enabled', $section->enabled ?? true))>Disabled</option>
        </select>
        @error('enabled')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>
