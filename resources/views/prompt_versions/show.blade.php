@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1>{{ $version->prompt_name }} <small class="text-muted">{{ $version->version_label }}</small></h1>
        <p class="mb-1">Status: <strong>{{ ucfirst($version->status) }}</strong></p>
        @if($version->notes)
            <p class="text-muted">{{ $version->notes }}</p>
        @endif
    </div>
    <div class="d-flex flex-column gap-2">
        <a href="{{ route('prompt-versions.edit', $version) }}" class="btn btn-outline-primary">Edit Version</a>
        <form method="post" action="{{ route('prompt-versions.duplicate', $version) }}">
            @csrf
            <button class="btn btn-outline-secondary">Duplicate</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Add Line</div>
    <div class="card-body">
        <form method="post" action="{{ route('prompt-lines.store') }}" class="row g-3">
            @csrf
            <input type="hidden" name="version_id" value="{{ $version->id }}">
            <div class="col-md-4">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-select">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->title ?? $section->key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Order</label>
                <input type="number" class="form-control" name="order_index" value="{{ old('order_index', 0) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Enabled</label>
                <select class="form-select" name="enabled">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="3">{{ old('content') }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Add Line</button>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <h2>Prompt Lines</h2>
    <p class="text-muted">Use the reorder form per section to update ordering.</p>
</div>

@foreach($sections as $section)
    @php
        $lines = $version->lines->where('section_id', $section->id);
    @endphp
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $section->title ?? $section->key }}</strong>
                <span class="text-muted">({{ $section->key }})</span>
            </div>
            <span class="badge bg-{{ $section->enabled ? 'success' : 'secondary' }}">{{ $section->enabled ? 'Enabled' : 'Disabled' }}</span>
        </div>
        <div class="card-body">
            @if($lines->isEmpty())
                <p class="text-muted">No lines for this section.</p>
            @else
                <form method="post" action="{{ route('prompt-lines.reorder', $version) }}" class="mb-3">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    <div class="mb-2">
                        <label class="form-label">Reorder IDs (comma separated)</label>
                        <input type="text" class="form-control" name="order" value="{{ $lines->pluck('id')->implode(',') }}">
                        <div class="form-text">Enter the line IDs in the desired order.</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">Save Order</button>
                </form>
                <div class="list-group">
                    @foreach($lines as $line)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">#{{ $line->id }} • Order {{ $line->order_index }}</small>
                                    <pre class="mb-0">{{ $line->content }}</pre>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $line->enabled ? 'success' : 'secondary' }}">{{ $line->enabled ? 'Enabled' : 'Disabled' }}</span>
                                    <div class="mt-2 d-flex flex-column gap-1">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('prompt-lines.edit', $line) }}">Edit</a>
                                        <form method="post" action="{{ route('prompt-lines.destroy', $line) }}" onsubmit="return confirm('Delete this line?');">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endforeach
@endsection
