<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_user_edit_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'StrongAdminPassword!',
            'role' => 'admin',
        ]);
        $controller = User::create([
            'name' => 'Night Operator',
            'role' => 'controller',
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $controller))
            ->assertOk()
            ->assertSee('Night Operator');
    }

    public function test_admin_can_edit_controller_and_replace_pin(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'StrongAdminPassword!',
            'role' => 'admin',
        ]);
        $controller = User::create([
            'name' => 'Night Operator',
            'role' => 'controller',
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $controller), [
                'name' => 'Night Shift Operator',
                'role' => 'controller',
                'username' => '',
                'password' => '',
                'pin' => '9876',
            ])
            ->assertRedirect(route('admin.users.index'));

        $controller->refresh();
        $this->assertSame('Night Shift Operator', $controller->name);
        $this->assertTrue(Hash::check('9876', $controller->pin_hash));
    }

    public function test_blank_password_and_pin_keep_existing_credentials(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'StrongAdminPassword!',
            'role' => 'admin',
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'password' => 'ExistingManagerPassword!',
            'role' => 'manager',
        ]);
        $oldPassword = $manager->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $manager), [
                'name' => 'Updated Manager',
                'role' => 'manager',
                'username' => 'manager',
                'password' => '',
                'pin' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $manager->refresh();
        $this->assertSame($oldPassword, $manager->password);
    }

    public function test_last_admin_cannot_be_demoted(): void
    {
        $admin = User::create([
            'name' => 'Only Admin',
            'username' => 'admin',
            'password' => 'StrongAdminPassword!',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => 'Only Admin',
                'role' => 'manager',
                'username' => 'admin',
                'password' => '',
                'pin' => '',
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('user');

        $this->assertSame('admin', $admin->fresh()->role);
    }
}
