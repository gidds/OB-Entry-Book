<?php

namespace Tests\Feature;

use App\Models\OccurrenceEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccurrenceEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_entry_text_is_required(): void
    {
        $response = $this->post('/entries', [
            'customer' => 'Test Customer',
            'entry_text' => '',
        ]);

        $response->assertSessionHasErrors('entry_text');
        $this->assertDatabaseCount('occurrence_entries', 0);
    }

    public function test_first_entry_uses_monthly_ob_number_format(): void
    {
        Carbon::setTestNow('2026-08-15 20:30:00');

        $response = $this->post('/entries', [
            'customer' => 'Autocast',
            'entry_text' => 'Security officer reported a delivery at the main gate.',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('occurrence_entries', [
            'ob_number' => '1\\8\\2026',
            'occurred_on' => '2026-08-15',
            'customer' => 'Autocast',
            'entry_text' => 'Security officer reported a delivery at the main gate.',
        ]);
    }

    public function test_ob_number_increments_within_same_month(): void
    {
        Carbon::setTestNow('2026-08-15 20:30:00');

        OccurrenceEntry::create([
            'ob_number' => '4\\8\\2026',
            'occurred_on' => '2026-08-14',
            'customer' => 'Existing',
            'entry_text' => 'Existing entry',
        ]);

        $this->post('/entries', [
            'customer' => 'Next',
            'entry_text' => 'Next entry',
        ]);

        $this->assertDatabaseHas('occurrence_entries', [
            'ob_number' => '5\\8\\2026',
        ]);
    }

    public function test_ob_number_resets_in_a_new_month(): void
    {
        OccurrenceEntry::create([
            'ob_number' => '27\\7\\2026',
            'occurred_on' => '2026-07-31',
            'customer' => 'July',
            'entry_text' => 'Last July entry',
        ]);

        Carbon::setTestNow('2026-08-01 00:05:00');

        $this->post('/entries', [
            'customer' => 'August',
            'entry_text' => 'First August entry',
        ]);

        $this->assertDatabaseHas('occurrence_entries', [
            'ob_number' => '1\\8\\2026',
        ]);
    }

    public function test_saved_entry_is_visible_on_home_page(): void
    {
        OccurrenceEntry::create([
            'ob_number' => '3\\8\\2026',
            'occurred_on' => '2026-08-15',
            'customer' => 'BBM',
            'entry_text' => 'Camera incident recorded by controller.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('3\\8\\2026');
        $response->assertSeeText('BBM');
        $response->assertSeeText('Camera incident recorded by controller.');
    }
}
