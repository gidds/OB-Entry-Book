<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_boots_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('OB Entries');
        $response->assertSeeText('No OB entries yet.');
    }
}
