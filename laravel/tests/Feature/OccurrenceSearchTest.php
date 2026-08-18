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

    public function test_dashboard_paginates_entries_instead_of_hiding_history(): void
    {
        for ($number = 3; $number <= 29; $number++) {
            OccurrenceEntry::create([
                'ob_number' => $number.'\\8\\2026',
                'occurred_on' => '2026-08-16',
                'customer' => 'Pagination Test',
                'entry_text' => 'Pagination marker '.$number,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSeeText('Page 1 of 2')
            ->assertSeeText('Pagination marker 29')
            ->assertDontSeeText('Delivery received at the main gate.');

        $this->get('/?page=2')
            ->assertOk()
            ->assertSeeText('Page 2 of 2')
            ->assertSeeText('Delivery received at the main gate.');
    }
}
