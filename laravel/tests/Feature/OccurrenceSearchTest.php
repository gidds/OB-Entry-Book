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

    public function test_dashboard_paginates_ob_entries_at_25_per_page(): void
    {
        foreach (range(1, 30) as $number) {
            OccurrenceEntry::create([
                'ob_number' => ($number + 2).'\\8\\2026',
                'occurred_on' => '2026-08-20',
                'customer' => 'Pagination Test',
                'entry_text' => 'Paginated entry '.$number,
            ]);
        }

        $firstPage = $this->get('/');

        $firstPage->assertOk()
            ->assertSeeText('Paginated entry 30')
            ->assertSeeText('Paginated entry 6')
            ->assertDontSeeText('Paginated entry 5')
            ->assertSeeText('Showing 1–25 of 32 entries')
            ->assertSeeText('Page 1 of 2')
            ->assertSee('page=2');

        $secondPage = $this->get('/?page=2');

        $secondPage->assertOk()
            ->assertSeeText('Paginated entry 5')
            ->assertSeeText('Paginated entry 1')
            ->assertSeeText('Delivery received at the main gate.')
            ->assertSeeText('Camera incident reported by the controller.')
            ->assertDontSeeText('Paginated entry 6')
            ->assertSeeText('Showing 26–32 of 32 entries')
            ->assertSeeText('Page 2 of 2');
    }

    public function test_search_query_is_preserved_in_pagination_links(): void
    {
        foreach (range(1, 30) as $number) {
            OccurrenceEntry::create([
                'ob_number' => ($number + 2).'\\8\\2026',
                'occurred_on' => '2026-08-20',
                'customer' => 'Paged Customer',
                'entry_text' => 'Search pagination entry '.$number,
            ]);
        }

        $this->get('/?q=Paged+Customer')
            ->assertOk()
            ->assertSee('q=Paged%20Customer')
            ->assertSee('page=2');
    }
}
