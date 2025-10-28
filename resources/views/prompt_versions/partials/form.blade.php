<div class="mb-3">
    <label class="form-label">Prompt Name</label>
    <input class="form-control" name="prompt_name" value="{{ old('prompt_name', $version->prompt_name ?? '') }}">
    @error('prompt_name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Version Label</label>
    <input class="form-control" name="version_label" value="{{ old('version_label', $version->version_label ?? '') }}">
    @error('version_label')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Status</label>
    <select class="form-select" name="status">
        @foreach($statuses as $status)
            <option value="{{ $status }}" @selected(old('status', $version->status ?? '') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea class="form-control" rows="3" name="notes">{{ old('notes', $version->notes ?? '') }}</textarea>
    @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
