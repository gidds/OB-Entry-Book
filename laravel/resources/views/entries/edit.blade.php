@extends('layouts.app')

@section('title', 'Edit OB Entry')

@section('content')
<section class="panel" style="max-width: 800px; margin: 0 auto;">
    <h1>Edit OB Entry</h1>
    <p class="muted">Only the customer/site and entry text can be changed during the one-hour editing window.</p>

    <div style="margin-top:1rem">
        <strong>OB {{ $entry->ob_number }}</strong>
        <span class="muted">· {{ $entry->occurred_on->format('d M Y') }} · Posted {{ $entry->created_at->format('H:i') }}</span>
    </div>

    <form method="post" action="{{ route('entries.update', $entry) }}">
        @csrf
        @method('PUT')

        <label for="customer">Customer</label>
        <input id="customer" name="customer" value="{{ old('customer', $entry->customer) }}" maxlength="255">

        <label for="entry_text">Entry</label>
        <textarea id="entry_text" name="entry_text" required>{{ old('entry_text', $entry->entry_text) }}</textarea>

        <label for="pin">Controller PIN</label>
        <input id="pin" name="pin" type="password" inputmode="numeric" autocomplete="off" required>

        <button type="submit">Save changes</button>
        <a class="button" href="{{ route('entries.index') }}">Cancel</a>
    </form>
</section>
@endsection
