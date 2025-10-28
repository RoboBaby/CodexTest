@extends('layouts.app')

@section('content')
<h1>Edit Prompt Version</h1>
<form method="post" action="{{ route('prompt-versions.update', $version) }}" class="mt-3">
    @csrf
    @method('put')
    @include('prompt_versions.partials.form')
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('prompt-versions.show', $version) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<form method="post" action="{{ route('prompt-versions.destroy', $version) }}" class="mt-3" onsubmit="return confirm('Delete this version?');">
    @csrf
    @method('delete')
    <button class="btn btn-outline-danger">Delete Version</button>
</form>
@endsection
