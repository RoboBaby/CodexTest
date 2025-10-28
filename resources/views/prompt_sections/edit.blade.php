@extends('layouts.app')

@section('content')
<h1>Edit Section</h1>
<form method="post" action="{{ route('prompt-sections.update', $section) }}" class="mt-3">
    @csrf
    @method('put')
    @include('prompt_sections.partials.form')
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('prompt-sections.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
