<?php

namespace Tests\Feature;

use App\Models\OccurrenceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccurrenceExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_export_form(): void
    {
        $this->get('/entries/export')->assertRedirect('/login');
    }

    public function test_controller_cannot_export_entries(): void
    {
        $controller = User::create([
            'name' => 'Controller',
            'username' => 'controller',
            'password' => 'controller-password',
            'role' => 'controller',
        ]);

        $this->actingAs($controller)
            ->get('/entries/export')
            ->assertForbidden();
    }

    public function test_both_dates_are_required(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)
            ->post('/entries/export', [])
            ->assertSessionHasErrors(['from_date', 'to_date']);
    }

    public function test_to_date_must_not_be_before_from_date(): void
    {
        $this->actingAs($this->manager())
            ->post('/entries/export', [
                'from_date' => '2026-08-20',
                'to_date' => '2026-08-19',
            ])
            ->assertSessionHasErrors(['to_date']);
    }

    public function test_manager_can_export_only_entries_in_selected_date_range(): void
    {
        $this->entry('1\\8\\2026', '2026-08-18', 'Before range');
        $this->entry('2\\8\\2026', '2026-08-19', 'First included');
        $this->entry('3\\8\\2026', '2026-08-20', 'Last included');
        $this->entry('4\\8\\2026', '2026-08-21', 'After range');

        $response = $this->actingAs($this->manager())
            ->post('/entries/export', [
                'from_date' => '2026-08-19',
                'to_date' => '2026-08-20',
            ]);

        $response->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename="ob-export-2026-08-19-to-2026-08-20.xml"')
            ->assertSeeText('First included')
            ->assertSeeText('Last included')
            ->assertDontSeeText('Before range')
            ->assertDontSeeText('After range');
    }

    private function manager(): User
    {
        return User::create([
            'name' => 'Manager',
            'username' => 'manager-'.uniqid(),
            'password' => 'manager-password-'.uniqid(),
            'role' => 'manager',
        ]);
    }

    private function entry(string $number, string $date, string $text): OccurrenceEntry
    {
        return OccurrenceEntry::create([
            'ob_number' => $number,
            'occurred_on' => $date,
            'customer' => 'Test Site',
            'entry_text' => $text,
        ]);
    }
}
