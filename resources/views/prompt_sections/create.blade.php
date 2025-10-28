@extends('layouts.app')

@section('content')
<h1>Create Section</h1>
<form method="post" action="{{ route('prompt-sections.store') }}" class="mt-3">
    @csrf
    @include('prompt_sections.partials.form')
    <button class="btn btn-primary">Create</button>
</form>
@endsection
