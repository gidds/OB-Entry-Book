<?php

namespace Tests\Feature;

use App\Models\OccurrenceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OccurrenceEntryEditingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_add_entry_page_contains_browser_local_draft_controls(): void
    {
        $this->get('/entries/create')
            ->assertOk()
            ->assertSeeText('Clear draft')
            ->assertSee('ob-book-entry-draft-v1', false)
            ->assertSeeText('Drafts are stored only in this browser.');
    }

    public function test_entry_can_be_opened_for_editing_during_first_hour(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');
        $entry = $this->entry();

        Carbon::setTestNow('2026-09-24 10:59:59');

        $this->get(route('entries.edit', $entry))
            ->assertOk()
            ->assertSeeText('Edit OB Entry')
            ->assertSeeText('OB 1\9\2026')
            ->assertSee('Original entry');
    }

    public function test_valid_controller_pin_updates_only_customer_and_entry_text(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');
        $entry = $this->entry();
        $this->controller('2468');

        Carbon::setTestNow('2026-09-24 10:30:00');

        $this->put(route('entries.update', $entry), [
            'customer' => 'Updated Site',
            'entry_text' => 'Corrected entry text.',
            'pin' => '2468',
            'ob_number' => '999\9\2026',
            'occurred_on' => '2030-01-01',
        ])->assertRedirect(route('entries.index'));

        $entry->refresh();

        $this->assertSame('Updated Site', $entry->customer);
        $this->assertSame('Corrected entry text.', $entry->entry_text);
        $this->assertSame('1\9\2026', $entry->ob_number);
        $this->assertSame('2026-09-24', $entry->occurred_on->format('Y-m-d'));
        $this->assertSame('2026-09-24 10:00:00', $entry->created_at->format('Y-m-d H:i:s'));
    }

    public function test_invalid_controller_pin_does_not_update_entry(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');
        $entry = $this->entry();
        $this->controller('2468');

        $this->from(route('entries.edit', $entry))
            ->put(route('entries.update', $entry), [
                'customer' => 'Changed Site',
                'entry_text' => 'Changed text.',
                'pin' => '9999',
            ])
            ->assertRedirect(route('entries.edit', $entry))
            ->assertSessionHasErrors('pin');

        $entry->refresh();

        $this->assertSame('Original Site', $entry->customer);
        $this->assertSame('Original entry', $entry->entry_text);
    }

    public function test_entry_is_locked_when_one_hour_has_elapsed(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');
        $entry = $this->entry();
        $this->controller('2468');

        Carbon::setTestNow('2026-09-24 11:00:00');

        $this->get(route('entries.edit', $entry))
            ->assertRedirect(route('entries.index'))
            ->assertSessionHasErrors('entry');

        $this->put(route('entries.update', $entry), [
            'customer' => 'Changed Site',
            'entry_text' => 'Changed text.',
            'pin' => '2468',
        ])->assertRedirect(route('entries.index'))
            ->assertSessionHasErrors('entry');

        $entry->refresh();

        $this->assertSame('Original Site', $entry->customer);
        $this->assertSame('Original entry', $entry->entry_text);
    }

    public function test_dashboard_only_shows_edit_action_during_edit_window(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');
        $entry = $this->entry();

        Carbon::setTestNow('2026-09-24 10:59:59');
        $this->get('/')->assertSee(route('entries.edit', $entry));

        Carbon::setTestNow('2026-09-24 11:00:00');
        $this->get('/')->assertDontSee(route('entries.edit', $entry));
    }

    private function entry(): OccurrenceEntry
    {
        return OccurrenceEntry::create([
            'ob_number' => '1\9\2026',
            'occurred_on' => '2026-09-24',
            'customer' => 'Original Site',
            'entry_text' => 'Original entry',
        ]);
    }

    private function controller(string $pin): User
    {
        return User::create([
            'name' => 'Test Controller',
            'role' => 'controller',
            'pin_hash' => Hash::make($pin),
        ]);
    }
}
