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
                        <span class="muted">· {{ $entry->occurred_on->format('d M Y') }} · {{ $entry->created_at->format('H:i') }}</span>
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

        @if($entries->total() > 0)
            <div class="panel" style="margin-top:1rem;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
                <div class="muted">
                    Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ $entries->total() }} entries · Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}
                </div>
                @if($entries->hasPages())
                    <div style="display:flex;gap:.5rem;align-items:center">
                        @if($entries->onFirstPage())
                            <span class="muted">Previous</span>
                        @else
                            <a class="button" style="margin-top:0" href="{{ $entries->previousPageUrl() }}">Previous</a>
                        @endif

                        @if($entries->hasMorePages())
                            <a class="button" style="margin-top:0" href="{{ $entries->nextPageUrl() }}">Next</a>
                        @else
                            <span class="muted">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        @endif
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

        <div class="panel" style="margin-bottom:1rem">
            <strong>Desktop alerts</strong>
            <div id="notification-status" class="muted" style="margin-top:.35rem">Checking browser notification permission…</div>
            <button id="enable-notifications" type="button" style="display:none">Enable desktop notifications</button>
        </div>

        <div class="panel" id="management-instructions">
            @forelse($instructions as $instruction)
                <article
                    class="entry instruction {{ $instruction->acknowledgements->isEmpty() ? 'pending' : '' }}"
                    data-instruction-id="{{ $instruction->id }}"
                    data-pending="{{ $instruction->acknowledgements->isEmpty() ? '1' : '0' }}"
                >
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
        const notificationLifetimeMs = 8000;
        const notificationStorageKey = 'ob-book-last-notified-instruction-id';
        const notificationBaselineKey = 'ob-book-notification-baseline-set';
        const editableElements = ['INPUT', 'TEXTAREA', 'SELECT'];
        const countdown = document.getElementById('refresh-countdown');
        const enableNotifications = document.getElementById('enable-notifications');
        const notificationStatus = document.getElementById('notification-status');
        const pendingInstructionIds = Array.from(document.querySelectorAll('[data-instruction-id][data-pending="1"]'))
            .map((element) => Number(element.dataset.instructionId))
            .filter(Number.isFinite)
            .sort((a, b) => b - a);
        const newestPendingInstructionId = pendingInstructionIds[0] ?? null;
        let remaining = refreshSeconds;

        const renderCountdown = () => {
            if (countdown) {
                countdown.textContent = String(remaining);
            }
        };

        const setNotificationStatus = (message) => {
            if (notificationStatus) {
                notificationStatus.textContent = message;
            }
        };

        const saveNotificationBaseline = () => {
            if (localStorage.getItem(notificationBaselineKey) === '1') {
                return;
            }

            if (newestPendingInstructionId !== null) {
                localStorage.setItem(notificationStorageKey, String(newestPendingInstructionId));
            }

            localStorage.setItem(notificationBaselineKey, '1');
        };

        const notifyForNewInstruction = () => {
            if (!('Notification' in window) || Notification.permission !== 'granted' || newestPendingInstructionId === null) {
                return;
            }

            const previousId = Number(localStorage.getItem(notificationStorageKey) || '0');

            if (newestPendingInstructionId <= previousId) {
                return;
            }

            const notification = new Notification('New Management Instruction', {
                body: 'A new instruction requires attention in the OB Book.',
                tag: `ob-instruction-${newestPendingInstructionId}`,
                renotify: false,
                requireInteraction: false,
            });

            localStorage.setItem(notificationStorageKey, String(newestPendingInstructionId));

            window.setTimeout(() => notification.close(), notificationLifetimeMs);

            notification.onclick = () => {
                window.focus();
                notification.close();
            };
        };

        const renderNotificationControls = () => {
            if (!('Notification' in window)) {
                setNotificationStatus('Desktop notifications are not supported by this browser.');
                return;
            }

            if (Notification.permission === 'granted') {
                setNotificationStatus('Desktop notifications are enabled.');
                if (enableNotifications) {
                    enableNotifications.style.display = 'none';
                }
                saveNotificationBaseline();
                notifyForNewInstruction();
                return;
            }

            if (Notification.permission === 'denied') {
                setNotificationStatus('Desktop notifications are blocked in the browser/site settings.');
                if (enableNotifications) {
                    enableNotifications.style.display = 'none';
                }
                return;
            }

            setNotificationStatus('Desktop notifications are not enabled yet.');
            if (enableNotifications) {
                enableNotifications.style.display = 'inline-block';
            }
        };

        if (enableNotifications) {
            enableNotifications.addEventListener('click', async () => {
                if (!('Notification' in window)) {
                    return;
                }

                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    saveNotificationBaseline();
                }

                renderNotificationControls();
            });
        }

        renderCountdown();
        renderNotificationControls();

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
