@extends('layouts.app')

@section('title', 'Control Room')

@section('content')
<div class="dashboard-grid">
    <section>
        <div class="top-actions">
            <div>
                <h1 style="margin:0">OB Entries</h1>
                <div class="muted">Latest occurrence-book entries · Refresh in <span id="refresh-countdown">30</span>s</div>
            </div>
            <a class="button" href="{{ route('entries.create') }}">Add Entry</a>
        </div>

        <div class="panel" style="margin-bottom:1rem">
            <form method="get" action="{{ route('entries.index') }}" style="display:flex;gap:.5rem;align-items:end;flex-wrap:wrap">
                <label for="q" style="flex:1;min-width:220px;margin-top:0">
                    Search OB history
                    <input id="q" name="q" value="{{ $search }}" placeholder="OB number, customer or entry text">
                </label>
                <button type="submit">Search</button>
                @if($search !== '')
                    <a class="button" href="{{ route('entries.index') }}">Clear</a>
                @endif
            </form>
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
                <p>{{ $search !== '' ? 'No OB entries matched your search.' : 'No OB entries yet.' }}</p>
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

<script>
    (() => {
        const refreshSeconds = 30;
        const editableElements = ['INPUT', 'TEXTAREA', 'SELECT'];
        const countdown = document.getElementById('refresh-countdown');
        let remaining = refreshSeconds;

        const renderCountdown = () => {
            if (countdown) {
                countdown.textContent = String(remaining);
            }
        };

        renderCountdown();

        window.setInterval(() => {
            const active = document.activeElement;
            const operatorIsEditing = active && editableElements.includes(active.tagName);

            if (operatorIsEditing) {
                remaining = refreshSeconds;
                renderCountdown();
                return;
            }

            remaining -= 1;
            renderCountdown();

            if (remaining <= 0) {
                window.location.reload();
            }
        }, 1000);
    })();
</script>
@endsection
