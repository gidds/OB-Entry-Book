<?php

namespace Tests\Feature;

use App\Models\OccurrenceEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccurrenceSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        OccurrenceEntry::create([
            'ob_number' => '1\\8\\2026',
            'occurred_on' => '2026-08-15',
            'customer' => 'Autocast',
            'entry_text' => 'Delivery received at the main gate.',
        ]);

        OccurrenceEntry::create([
            'ob_number' => '2\\8\\2026',
            'occurred_on' => '2026-08-15',
            'customer' => 'BBM',
            'entry_text' => 'Camera incident reported by the controller.',
        ]);
    }

    public function test_search_matches_customer(): void
    {
        $this->get('/?q=Autocast')
            ->assertOk()
            ->assertSeeText('Delivery received at the main gate.')
            ->assertDontSeeText('Camera incident reported by the controller.');
    }

    public function test_search_matches_ob_number(): void
    {
        $this->get('/?q=2%5C8%5C2026')
            ->assertOk()
            ->assertSeeText('BBM')
            ->assertDontSeeText('Autocast');
    }

    public function test_search_matches_entry_text(): void
    {
        $this->get('/?q=Camera')
            ->assertOk()
            ->assertSeeText('Camera incident reported by the controller.')
            ->assertDontSeeText('Delivery received at the main gate.');
    }
}
