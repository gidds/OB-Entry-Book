<?php

namespace Tests\Feature;

use App\Models\ManagementInstruction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    private function manager(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Manager One',
            'username' => 'manager1',
            'password' => 'Secret123!',
            'role' => 'manager',
        ], $overrides));
    }

    private function controller(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Controller One',
            'username' => 'controller1',
            'password' => 'Controller123!',
            'role' => 'controller',
            'pin_hash' => Hash::make('2468'),
        ], $overrides));
    }

    public function test_management_login_page_is_available(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSeeText('Management Login');
    }

    public function test_manager_can_log_in(): void
    {
        $manager = $this->manager();

        $response = $this->post('/login', [
            'username' => 'manager1',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($manager);
    }

    public function test_controller_credentials_cannot_open_management_session(): void
    {
        $this->controller();

        $response = $this->post('/login', [
            'username' => 'controller1',
            'password' => 'Controller123!',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_management_instruction_form(): void
    {
        $this->get('/instructions/create')
            ->assertRedirect('/login');
    }

    public function test_manager_can_create_instruction(): void
    {
        $manager = $this->manager();

        $response = $this->actingAs($manager)->post('/instructions', [
            'instruction_text' => 'All after-hours collections require management confirmation.',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('management_instructions', [
            'manager_id' => $manager->id,
            'manager_name' => 'Manager One',
            'instruction_text' => 'All after-hours collections require management confirmation.',
        ]);
    }

    public function test_valid_controller_pin_acknowledges_instruction(): void
    {
        $controller = $this->controller();
        $instruction = ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_name' => 'Manager One',
            'instruction_text' => 'Check gate lock after final patrol.',
        ]);

        $response = $this->post('/instructions/'.$instruction->id.'/acknowledge', [
            'pin' => '2468',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('instruction_acknowledgements', [
            'management_instruction_id' => $instruction->id,
            'user_id' => $controller->id,
            'operator_name' => 'Controller One',
        ]);
    }

    public function test_invalid_controller_pin_does_not_acknowledge_instruction(): void
    {
        $this->controller();
        $instruction = ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_name' => 'Manager One',
            'instruction_text' => 'Check gate lock after final patrol.',
        ]);

        $response = $this->post('/instructions/'.$instruction->id.'/acknowledge', [
            'pin' => '9999',
        ]);

        $response->assertSessionHasErrors('pin');
        $this->assertDatabaseCount('instruction_acknowledgements', 0);
    }

    public function test_dashboard_shows_pending_and_acknowledged_instruction_states(): void
    {
        $controller = $this->controller();

        $pending = ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_name' => 'Manager One',
            'instruction_text' => 'Pending instruction text.',
        ]);

        $acknowledged = ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_name' => 'Manager Two',
            'instruction_text' => 'Acknowledged instruction text.',
        ]);

        $acknowledged->acknowledgements()->create([
            'user_id' => $controller->id,
            'operator_name' => $controller->name,
            'acknowledged_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText($pending->instruction_text);
        $response->assertSeeText('Controller PIN');
        $response->assertSeeText($acknowledged->instruction_text);
        $response->assertSeeText('Acknowledged by Controller One');
    }

    public function test_dashboard_includes_desktop_notification_hooks_for_pending_instructions(): void
    {
        $instruction = ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_name' => 'Manager One',
            'instruction_text' => 'Sensitive instruction details stay inside the OB Book.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('Desktop alerts');
        $response->assertSeeText('Enable desktop notifications');
        $response->assertSee('data-instruction-id="'.$instruction->id.'"', false);
        $response->assertSee('data-pending="1"', false);
        $response->assertSee('A new instruction requires attention in the OB Book.', false);
        $response->assertSee('requireInteraction: false', false);
        $response->assertSee('notificationLifetimeMs = 8000', false);
    }
}
