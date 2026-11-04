<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
        Notification::fake();
    }

    private function student(): User
    {
        return User::factory()->create(['password' => Hash::make('gammes2026')]);
    }

    public function test_the_change_password_page_is_displayed(): void
    {
        $this->actingAs($this->student())
            ->get('/profil/mot-de-passe')
            ->assertOk()
            ->assertSee('Mot de passe actuel')
            ->assertSee('8 caractères minimum');
    }

    public function test_a_student_can_change_their_password(): void
    {
        $user = $this->student();
        $oldRememberToken = $user->remember_token;

        $this->actingAs($user)
            ->put('/profil/mot-de-passe', ['current_password' => 'gammes2026', 'password' => 'arpeges2027'])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('arpeges2027', $user->password));
        // Les appareils restés connectés avec « Se souvenir de moi » sont déconnectés…
        $this->assertNotSame($oldRememberToken, $user->remember_token);
        // … mais cet appareil-ci reste connecté.
        $this->assertAuthenticatedAs($user);

        Notification::assertSentTo($user, PasswordChanged::class);
    }

    public function test_the_current_password_is_checked(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->put('/profil/mot-de-passe', ['current_password' => 'pas le bon', 'password' => 'arpeges2027'])
            ->assertSessionHasErrors(['current_password' => 'Ce n’est pas ton mot de passe actuel.']);

        $this->assertTrue(Hash::check('gammes2026', $user->fresh()->password));
        Notification::assertNothingSent();
    }

    public function test_the_new_password_follows_the_rules_and_must_change(): void
    {
        $user = $this->student();

        foreach (['court', 'gammes2026'] as $password) {
            $this->actingAs($user)
                ->put('/profil/mot-de-passe', ['current_password' => 'gammes2026', 'password' => $password])
                ->assertSessionHasErrors('password');
        }

        $this->assertTrue(Hash::check('gammes2026', $user->fresh()->password));
    }

    public function test_the_other_devices_are_logged_out(): void
    {
        // Sessions enregistrées dans la base, comme sur le serveur.
        config(['session.driver' => 'database']);

        $user = $this->student();
        $someoneElse = User::factory()->create();

        foreach (['telephone-perdu' => $user->id, 'ordinateur' => $user->id, 'autre-eleve' => $someoneElse->id] as $id => $userId) {
            DB::table('sessions')->insert([
                'id' => $id, 'user_id' => $userId, 'payload' => '', 'last_activity' => time(),
            ]);
        }

        $this->actingAs($user)
            ->put('/profil/mot-de-passe', ['current_password' => 'gammes2026', 'password' => 'arpeges2027'])
            ->assertRedirect(route('profile'));

        $this->assertDatabaseMissing('sessions', ['id' => 'telephone-perdu']);
        $this->assertDatabaseMissing('sessions', ['id' => 'ordinateur']);
        $this->assertDatabaseHas('sessions', ['id' => 'autre-eleve']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_guessing_the_current_password_is_slowed_down(): void
    {
        $user = $this->student();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)->put('/profil/mot-de-passe', ['current_password' => 'essai '.$i, 'password' => 'arpeges2027']);
        }

        $this->actingAs($user)
            ->put('/profil/mot-de-passe', ['current_password' => 'gammes2026', 'password' => 'arpeges2027'])
            ->assertTooManyRequests();

        $this->assertTrue(Hash::check('gammes2026', $user->fresh()->password));
    }
}
