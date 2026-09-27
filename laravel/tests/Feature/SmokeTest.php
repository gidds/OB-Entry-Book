<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_boots_successfully(): void
    {
        $controller = User::create([
            'name' => 'Smoke Test Controller',
            'role' => 'controller',
        ]);

        $response = $this->actingAs($controller)->get('/');

        $response->assertOk();
        $response->assertSeeText('OB Entries');
        $response->assertSeeText('No OB entries yet.');
    }
}
