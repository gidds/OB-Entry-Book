@extends('layouts.app')

@section('title', 'Add OB Entry')

@section('content')
<section class="panel" style="max-width: 800px; margin: 0 auto;">
    <h1>Add OB Entry</h1>
    <p class="muted">The date and OB number are generated automatically when the entry is saved.</p>

    <form method="post" action="{{ route('entries.store') }}">
        @csrf

        <label for="customer">Customer</label>
        <input id="customer" name="customer" value="{{ old('customer') }}" maxlength="255">

        <label for="entry_text">Entry</label>
        <textarea id="entry_text" name="entry_text" required>{{ old('entry_text') }}</textarea>

        <button type="submit">Submit Entry</button>
    </form>
</section>
@endsection
