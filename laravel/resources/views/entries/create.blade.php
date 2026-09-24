@extends('layouts.app')

@section('title', 'Add OB Entry')

@section('content')
<section class="panel" style="max-width: 800px; margin: 0 auto;">
    <h1>Add OB Entry</h1>
    <p class="muted">The date and OB number are generated automatically when the entry is saved.</p>

    <form id="ob-entry-form" method="post" action="{{ route('entries.store') }}">
        @csrf

        <label for="customer">Customer</label>
        <input id="customer" name="customer" value="{{ old('customer') }}" maxlength="255">

        <label for="entry_text">Entry</label>
        <textarea id="entry_text" name="entry_text" required>{{ old('entry_text') }}</textarea>

        <button type="submit">Submit Entry</button>
        <button id="clear-draft" type="button">Clear draft</button>
        <div id="draft-status" class="muted" style="margin-top:.75rem">Drafts are stored only in this browser.</div>
    </form>
</section>

<script>
    (() => {
        const storageKey = 'ob-book-entry-draft-v1';
        const form = document.getElementById('ob-entry-form');
        const customer = document.getElementById('customer');
        const entryText = document.getElementById('entry_text');
        const clearDraft = document.getElementById('clear-draft');
        const draftStatus = document.getElementById('draft-status');

        if (!form || !customer || !entryText || !clearDraft || !draftStatus) {
            return;
        }

        const saveDraft = () => {
            localStorage.setItem(storageKey, JSON.stringify({
                customer: customer.value,
                entry_text: entryText.value,
            }));
            draftStatus.textContent = 'Draft saved in this browser.';
        };

        if (customer.value === '' && entryText.value === '') {
            try {
                const draft = JSON.parse(localStorage.getItem(storageKey) || 'null');
                if (draft) {
                    customer.value = draft.customer || '';
                    entryText.value = draft.entry_text || '';
                    draftStatus.textContent = 'Browser draft restored.';
                }
            } catch {
                localStorage.removeItem(storageKey);
            }
        } else {
            saveDraft();
        }

        customer.addEventListener('input', saveDraft);
        entryText.addEventListener('input', saveDraft);

        clearDraft.addEventListener('click', () => {
            customer.value = '';
            entryText.value = '';
            localStorage.removeItem(storageKey);
            draftStatus.textContent = 'Draft cleared.';
            customer.focus();
        });

    })();
</script>
@endsection
