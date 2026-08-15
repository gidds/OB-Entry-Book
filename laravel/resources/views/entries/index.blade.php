@extends('layouts.app')

@section('title', 'Control Room')

@section('content')
<div class="dashboard-grid">
    <section>
        <div class="top-actions">
            <div>
                <h1 style="margin:0">OB Entries</h1>
                <div class="muted">Latest occurrence-book entries</div>
            </div>
            <a class="button" href="{{ route('entries.create') }}">Add Entry</a>
        </div>

        <div class="panel">
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
        </div>
    </section>

    <aside>
        <div class="top-actions">
            <div>
                <h2 style="margin:0">Management Instructions</h2>
                <div class="muted">Pending instructions are highlighted</div>
            </div>
            @auth
                @if(auth()->user()->isManagement())
                    <a class="button" href="{{ route('instructions.create') }}">Add</a>
                @endif
            @endauth
        </div>

        <div class="panel">
            @forelse($instructions as $instruction)
                <article class="entry instruction {{ $instruction->acknowledgements->isEmpty() ? 'pending' : '' }}">
                    <div>
                        <strong>{{ $instruction->manager_name }}</strong>
                        <span class="muted">· {{ $instruction->instruction_date->format('d M Y') }}</span>
                    </div>
                    <p>{{ $instruction->instruction_text }}</p>

                    @if($instruction->acknowledgements->isEmpty())
                        <form method="post" action="{{ route('instructions.acknowledge', $instruction) }}">
                            @csrf
                            <label for="pin-{{ $instruction->id }}">Controller PIN</label>
                            <input id="pin-{{ $instruction->id }}" name="pin" type="password" inputmode="numeric" autocomplete="off" required>
                            <button type="submit">ACK</button>
                        </form>
                    @else
                        <div class="muted">
                            Acknowledged by {{ $instruction->acknowledgements->pluck('operator_name')->join(', ') }}
                        </div>
                    @endif
                </article>
            @empty
                <p>No management instructions.</p>
            @endforelse
        </div>
    </aside>
</div>
@endsection
