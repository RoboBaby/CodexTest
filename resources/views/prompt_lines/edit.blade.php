@extends('layouts.app')

@section('content')
<h1>Edit Prompt Line</h1>
<form method="post" action="{{ route('prompt-lines.update', $line) }}" class="mt-3">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Version</label>
            <select class="form-select" name="version_id">
                @foreach($versions as $version)
                    <option value="{{ $version->id }}" @selected(old('version_id', $line->version_id) == $version->id)>
                        {{ $version->prompt_name }} ({{ $version->version_label }})
                    </option>
                @endforeach
            </select>
            @error('version_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Section</label>
            <select class="form-select" name="section_id">
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id', $line->section_id) == $section->id)>
                        {{ $section->title ?? $section->key }}
                    </option>
                @endforeach
            </select>
            @error('section_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Order</label>
            <input type="number" class="form-control" name="order_index" value="{{ old('order_index', $line->order_index) }}">
            @error('order_index')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Enabled</label>
            <select class="form-select" name="enabled">
                <option value="1" @selected(old('enabled', $line->enabled))>Yes</option>
                <option value="0" @selected(!old('enabled', $line->enabled))>No</option>
            </select>
            @error('enabled')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Content</label>
        <textarea class="form-control" name="content" rows="5">{{ old('content', $line->content) }}</textarea>
        @error('content')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('prompt-versions.show', $line->version_id) }}" class="btn btn-secondary">Back</a>
    </div>
</form>
@endsection
