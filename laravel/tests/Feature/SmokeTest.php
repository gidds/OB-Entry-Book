<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_home_page_boots_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('OB Entry Book Laravel rebuild OK');
    }
}
