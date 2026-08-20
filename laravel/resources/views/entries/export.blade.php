@extends('layouts.app')

@section('title', 'Export OB Entries')

@section('content')
<div class="panel" style="max-width:720px;margin:0 auto">
    <h1 style="margin-top:0">Export OB Entries</h1>
    <p class="muted">Choose the date range to export. Both dates are required and are included in the export.</p>

    <form method="post" action="{{ route('entries.export.store') }}">
        @csrf

        <label for="from_date">From date</label>
        <input id="from_date" name="from_date" type="date" value="{{ old('from_date') }}" required>

        <label for="to_date">To date</label>
        <input id="to_date" name="to_date" type="date" value="{{ old('to_date') }}" required>

        <button type="submit">Export XML</button>
        <a class="button" href="{{ route('entries.index') }}">Cancel</a>
    </form>
</div>
@endsection
