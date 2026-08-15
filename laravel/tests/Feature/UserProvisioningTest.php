<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_be_provisioned_with_hashed_password(): void
    {
        $this->artisan('ob:create-user', [
            'name' => 'Operations Manager',
            'role' => 'manager',
            '--username' => 'opsmanager',
            '--password' => 'StrongTestPassword!',
        ])->assertSuccessful();

        $user = User::where('username', 'opsmanager')->firstOrFail();

        $this->assertSame('manager', $user->role);
        $this->assertNotSame('StrongTestPassword!', $user->password);
        $this->assertTrue(Hash::check('StrongTestPassword!', $user->password));
    }

    public function test_controller_can_be_provisioned_with_hashed_pin(): void
    {
        $this->artisan('ob:create-user', [
            'name' => 'Night Controller',
            'role' => 'controller',
            '--pin' => '1357',
        ])->assertSuccessful();

        $user = User::where('name', 'Night Controller')->firstOrFail();

        $this->assertNull($user->username);
        $this->assertTrue(Hash::check('1357', $user->pin_hash));
    }

    public function test_invalid_role_is_rejected(): void
    {
        $this->artisan('ob:create-user', [
            'name' => 'Invalid User',
            'role' => 'superhero',
        ])->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
