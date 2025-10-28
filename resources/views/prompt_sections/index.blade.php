@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Prompt Sections</h1>
    <a href="{{ route('prompt-sections.create') }}" class="btn btn-primary">New Section</a>
</div>
<div class="card mb-4">
    <div class="card-header">Reorder Sections</div>
    <div class="card-body">
        <form method="post" action="{{ route('prompt-sections.reorder') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-12">
                <label class="form-label">Section IDs (comma separated)</label>
                <input type="text" class="form-control" name="order" value="{{ $sections->pluck('id')->implode(',') }}">
                <div class="form-text">Provide the section IDs in the desired order.</div>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary">Save Order</button>
            </div>
        </form>
    </div>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Key</th>
        <th>Title</th>
        <th>Order</th>
        <th>Status</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($sections as $section)
        <tr>
            <td>{{ $section->id }}</td>
            <td>{{ $section->key }}</td>
            <td>{{ $section->title }}</td>
            <td>{{ $section->order_index }}</td>
            <td>
                <span class="badge bg-{{ $section->enabled ? 'success' : 'secondary' }}">{{ $section->enabled ? 'Enabled' : 'Disabled' }}</span>
            </td>
            <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('prompt-sections.edit', $section) }}">Edit</a>
                <form method="post" action="{{ route('prompt-sections.destroy', $section) }}" class="d-inline" onsubmit="return confirm('Delete this section?');">
                    @csrf
                    @method('delete')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
