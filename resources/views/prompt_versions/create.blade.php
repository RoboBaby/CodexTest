@extends('layouts.app')

@section('content')
<h1>Create Prompt Version</h1>
<form method="post" action="{{ route('prompt-versions.store') }}" class="mt-3">
    @csrf
    @include('prompt_versions.partials.form')
    <button class="btn btn-primary">Create</button>
</form>
@endsection
