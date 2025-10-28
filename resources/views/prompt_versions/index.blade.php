@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Prompt Versions</h1>
    <a href="{{ route('prompt-versions.create') }}" class="btn btn-primary">New Version</a>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>Label</th>
        <th>Status</th>
        <th>Updated</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($versions as $version)
        <tr>
            <td>{{ $version->prompt_name }}</td>
            <td>{{ $version->version_label }}</td>
            <td>{{ ucfirst($version->status) }}</td>
            <td>{{ $version->updated_at?->diffForHumans() }}</td>
            <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('prompt-versions.show', $version) }}">View</a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('prompt-versions.edit', $version) }}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $versions->links() }}
@endsection
