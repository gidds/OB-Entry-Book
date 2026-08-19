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

    public function test_admin_cannot_create_user_with_an_existing_password(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'SharedPassword123!',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->post(route('admin.users.store'), [
                'name' => 'Manager',
                'role' => 'manager',
                'username' => 'manager',
                'password' => 'SharedPassword123!',
                'pin' => '',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['username' => 'manager']);
    }

    public function test_admin_cannot_change_user_to_another_users_password(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'AdminPassword123!',
            'role' => 'admin',
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'password' => 'ManagerPassword123!',
            'role' => 'manager',
        ]);
        $oldPassword = $manager->password;

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $manager))
            ->put(route('admin.users.update', $manager), [
                'name' => 'Manager',
                'role' => 'manager',
                'username' => 'manager',
                'password' => 'AdminPassword123!',
                'pin' => '',
            ])
            ->assertRedirect(route('admin.users.edit', $manager))
            ->assertSessionHasErrors('password');

        $this->assertSame($oldPassword, $manager->fresh()->password);
    }

    public function test_user_can_reenter_their_own_current_password_without_duplicate_error(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'AdminPassword123!',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => 'Admin',
                'role' => 'admin',
                'username' => 'admin',
                'password' => 'AdminPassword123!',
                'pin' => '',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionDoesntHaveErrors('password');
    }
}
