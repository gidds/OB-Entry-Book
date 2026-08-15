@extends('layouts.app')

@section('title', 'OB Entries')

@section('content')
<div class="top-actions">
    <div>
        <h1 style="margin:0">OB Entries</h1>
        <div class="muted">Latest occurrence-book entries</div>
    </div>
    <a class="button" href="{{ route('entries.create') }}">Add Entry</a>
</div>

<section class="panel">
    @forelse($entries as $entry)
        <article class="entry">
            <div>
                <strong>{{ $entry->ob_number }}</strong>
                <span class="muted">· {{ $entry->occurred_on->format('d M Y') }}</span>
            </div>
            @if($entry->customer)
                <div><strong>Customer:</strong> {{ $entry->customer }}</div>
            @endif
            <p>{{ $entry->entry_text }}</p>
        </article>
    @empty
        <p>No OB entries yet.</p>
    @endforelse
</section>
@endsection
