@extends('layouts.app')

@section('title', 'Add Management Instruction')

@section('content')
<section class="panel" style="max-width: 800px; margin: 0 auto;">
    <h1>Add Management Instruction</h1>
    <p class="muted">Manager: {{ auth()->user()->name }} · Date: {{ now()->format('d M Y') }}</p>

    <form method="post" action="{{ route('instructions.store') }}">
        @csrf

        <label for="instruction_text">Instruction</label>
        <textarea id="instruction_text" name="instruction_text" required>{{ old('instruction_text') }}</textarea>

        <button type="submit">Add Instruction</button>
    </form>
</section>
@endsection
