<?php

namespace Tests\Feature;

use App\Models\ManagementInstruction;
use App\Models\OccurrenceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_operational_ob_routes(): void
    {
        $entry = OccurrenceEntry::create([
            'ob_number' => '1\9\2026',
            'occurred_on' => '2026-09-27',
            'customer' => 'Protected Site',
            'entry_text' => 'Protected entry',
        ]);
        $instruction = ManagementInstruction::create([
            'instruction_date' => '2026-09-27',
            'manager_name' => 'Manager',
            'instruction_text' => 'Protected instruction',
        ]);

        $this->get('/')->assertRedirect('/login');
        $this->get('/entries/create')->assertRedirect('/login');
        $this->post('/entries', [
            'customer' => 'Unauthorized',
            'entry_text' => 'Must not be saved',
        ])->assertRedirect('/login');
        $this->get(route('entries.edit', $entry))->assertRedirect('/login');
        $this->put(route('entries.update', $entry), [
            'customer' => 'Unauthorized',
            'entry_text' => 'Must not be saved',
            'pin' => '2468',
        ])->assertRedirect('/login');
        $this->post(route('instructions.acknowledge', $instruction), [
            'pin' => '2468',
        ])->assertRedirect('/login');

        $this->assertDatabaseCount('occurrence_entries', 1);
        $this->assertDatabaseCount('instruction_acknowledgements', 0);
    }

    public function test_controller_returns_to_intended_page_after_pin_login(): void
    {
        $this->controller();

        $this->get('/entries/create')->assertRedirect('/login');

        $this->post('/login/controller', ['pin' => '2468'])
            ->assertRedirect('/entries/create');
    }

    public function test_controller_pin_login_is_rate_limited(): void
    {
        $this->controller();

        foreach (range(1, 5) as $_) {
            $this->post('/login/controller', ['pin' => '9999'])
                ->assertSessionHasErrors('pin');
        }

        $this->post('/login/controller', ['pin' => '9999'])
            ->assertStatus(429);
    }

    private function controller(): User
    {
        return User::create([
            'name' => 'Boundary Test Controller',
            'role' => 'controller',
            'pin_hash' => Hash::make('2468'),
        ]);
    }
}
