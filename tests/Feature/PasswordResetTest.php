<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
        Notification::fake();
    }

    public function test_the_forgotten_password_page_is_displayed(): void
    {
        $this->get('/mot-de-passe-oublie')->assertOk()->assertSee('Recevoir le lien');
    }

    public function test_a_link_is_sent_to_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/mot-de-passe-oublie', ['email' => 'Zoe@Example.com'])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_the_answer_is_the_same_when_no_account_exists(): void
    {
        $response = $this->post('/mot-de-passe-oublie', ['email' => 'personne@example.com']);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
        Notification::assertNothingSent();
    }

    public function test_the_link_lets_the_student_choose_a_new_password(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'zoe@example.com']);

        $this->post('/mot-de-passe-oublie', ['email' => 'zoe@example.com']);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->get('/nouveau-mot-de-passe/'.$notification->token.'?email='.$user->email)
                ->assertOk()
                ->assertSee('Nouveau mot de passe');

            $this->post('/nouveau-mot-de-passe', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'arpeges2027',
            ])->assertRedirect(route('login'));

            return true;
        });

        $user->refresh();
        $this->assertTrue(Hash::check('arpeges2027', $user->password));
        // Le lien est arrivé dans sa boîte mail : l'adresse est confirmée au passage.
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_a_wrong_token_is_refused(): void
    {
        $user = User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/nouveau-mot-de-passe', [
            'token' => 'faux-jeton',
            'email' => 'zoe@example.com',
            'password' => 'arpeges2027',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_the_new_password_follows_the_same_rules(): void
    {
        User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/nouveau-mot-de-passe', [
            'token' => 'peu-importe',
            'email' => 'zoe@example.com',
            'password' => 'court',
        ])->assertSessionHasErrors('password');
    }
}
