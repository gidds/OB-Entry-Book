<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionExpirationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Laravel normally bypasses CSRF in tests; exercise the real verifier.
        $this->app->bind(ValidateCsrfToken::class, fn () => new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
    }

    public function test_expired_session_submission_redirects_and_displays_message_without_saving(): void
    {
        $this->withSession(['_token' => 'fresh-session-token'])
            ->post('/entries', ['_token' => 'expired-page-token', 'customer' => 'Site', 'entry_text' => 'Rejected'])
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'Your session expired. Please sign in again.')
            ->assertSessionMissing('_old_input');

        $this->assertDatabaseCount('occurrence_entries', 0);
        $this->get('/login')->assertOk()->assertSee('Your session expired. Please sign in again.');
    }

    public function test_stale_login_and_logout_forms_recover_without_flashing_credentials(): void
    {
        foreach (['/login', '/login/controller', '/logout'] as $url) {
            $this->withSession(['_token' => 'fresh-token'])
                ->post($url, ['_token' => 'stale-token', 'password' => 'secret', 'pin' => '1234'])
                ->assertRedirect('/login')
                ->assertSessionHas('status', 'Your session expired. Please sign in again.')
                ->assertSessionMissing('_old_input');
        }
    }

    public function test_json_stale_submission_keeps_error_status_and_provides_login_destination(): void
    {
        $this->withSession(['_token' => 'fresh-token'])
            ->postJson('/entries', ['_token' => 'stale-token'])
            ->assertStatus(419)
            ->assertJson(['message' => 'Your session expired. Please sign in again.', 'redirect' => route('login')]);
        $this->assertDatabaseCount('occurrence_entries', 0);
    }

    public function test_stale_put_and_missing_token_are_rejected_before_route_actions(): void
    {
        $this->withSession(['_token' => 'fresh-token'])
            ->put('/entries/999', ['_token' => 'stale-token'])
            ->assertRedirect('/login');
        $this->post('/entries', ['entry_text' => 'No token'])
            ->assertRedirect('/login');
        $this->assertDatabaseCount('occurrence_entries', 0);
    }

    public function test_expired_session_get_uses_auth_redirect_and_preserves_intended_page(): void
    {
        $this->withSession(['_token' => 'fresh-token'])->get('/entries/create')
            ->assertRedirect('/login')->assertSessionHas('url.intended', url('/entries/create'));
    }

    public function test_authenticated_invalid_csrf_is_still_rejected(): void
    {
        $user = User::create(['name' => 'Controller', 'role' => 'controller']);
        $this->actingAs($user)->withSession(['_token' => 'fresh-token'])
            ->post('/entries', ['_token' => 'invalid-token', 'customer' => 'Site', 'entry_text' => 'Rejected'])
            ->assertStatus(419);
        $this->assertDatabaseCount('occurrence_entries', 0);
    }

    public function test_authenticated_valid_csrf_submission_still_works(): void
    {
        $user = User::create(['name' => 'Controller', 'role' => 'controller']);
        $this->actingAs($user)->withSession(['_token' => 'valid-token'])
            ->post('/entries', ['_token' => 'valid-token', 'customer' => 'Site', 'entry_text' => 'Accepted'])
            ->assertRedirect();
        $this->assertDatabaseHas('occurrence_entries', ['entry_text' => 'Accepted']);
    }
}
