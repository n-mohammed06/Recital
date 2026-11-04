<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Réponses du faux service des mots de passe piratés (fin d'empreinte SHA-1 : nombre de fuites).
     *
     * @var array<int, string>
     */
    private array $leakedPasswords = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Les tests ne passent pas par Internet : la vérification DNS des adresses e-mail est simulée,
        // et le service des mots de passe piratés est remplacé par une fausse réponse.
        Validator::fakeDnsLookups();
        Http::preventStrayRequests();
        Http::fake(['api.pwnedpasswords.com/*' => fn () => Http::response(implode("\n", $this->leakedPasswords))]);
        Notification::fake();
    }

    /**
     * Un formulaire d'inscription valide.
     *
     * @return array<string, string>
     */
    private function validRegistration(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Zoé',
            'last_name' => 'N’Diaye-Martin',
            'email' => 'Zoe.Martin@Example.com',
            'password' => 'gammes2026',
            'terms' => '1',
            'age' => '1',
        ], $overrides);
    }

    public function test_the_register_page_is_displayed(): void
    {
        $this->get('/inscription')
            ->assertOk()
            ->assertSee('Créer mon compte')
            ->assertSee('8 caractères minimum')
            ->assertSee('Du premier Do à ton récital.');
    }

    public function test_the_login_page_has_the_show_password_button_and_the_forgotten_password_link(): void
    {
        $this->get('/connexion')
            ->assertOk()
            ->assertSee('Afficher le mot de passe')
            ->assertSee('Mot de passe oublié');
    }

    public function test_a_visitor_can_register_and_receives_the_confirmation_link(): void
    {
        $response = $this->post('/inscription', $this->validRegistration());

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        // L'adresse est enregistrée en minuscules, le mot de passe est haché, l'adresse n'est pas encore confirmée.
        $user = User::where('email', 'zoe.martin@example.com')->firstOrFail();
        $this->assertSame('Zoé', $user->first_name);
        $this->assertSame('N’Diaye-Martin', $user->last_name);
        $this->assertNotSame('gammes2026', $user->password);
        $this->assertTrue(Hash::check('gammes2026', $user->password));
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_every_field_and_both_boxes_are_required(): void
    {
        $this->post('/inscription', [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'password', 'terms', 'age']);

        $this->assertGuest();
    }

    public function test_the_password_needs_8_characters_a_letter_and_a_digit(): void
    {
        foreach (['piano12', 'seulementdeslettres', '12345678'] as $password) {
            $this->post('/inscription', $this->validRegistration(['password' => $password]))
                ->assertSessionHasErrors('password');
        }

        $this->assertGuest();
    }

    public function test_a_password_found_in_a_data_leak_is_refused(): void
    {
        // Le service répond avec la fin de l'empreinte SHA-1 du mot de passe : il est connu des pirates.
        $this->leakedPasswords[] = substr(strtoupper(sha1('azerty123')), 5).':12345';

        $this->post('/inscription', $this->validRegistration(['password' => 'azerty123']))
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_names_only_accept_letters_spaces_hyphens_and_apostrophes(): void
    {
        $this->post('/inscription', $this->validRegistration(['first_name' => '<script>']))
            ->assertSessionHasErrors('first_name');

        $this->assertGuest();
    }

    public function test_an_email_can_only_be_used_once(): void
    {
        User::factory()->create(['email' => 'zoe.martin@example.com']);

        // Même adresse avec des majuscules : c'est le même compte.
        $this->post('/inscription', $this->validRegistration(['email' => 'ZOE.MARTIN@example.com']))
            ->assertSessionHasErrors('email');
    }

    public function test_a_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/connexion', ['email' => 'Zoe@Example.com', 'password' => 'password'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        // La date de connexion est notée (comptes inactifs supprimés après 2 ans).
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->get('/')->assertOk()->assertSee('Bonjour '.$user->first_name);

        $this->post('/deconnexion')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_a_wrong_password_shows_a_vague_message(): void
    {
        User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'pas le bon'])
            ->assertSessionHasErrors(['login' => 'Adresse e-mail ou mot de passe incorrect.']);

        $this->assertGuest();
    }

    public function test_a_student_never_waits_after_a_few_mistakes(): void
    {
        $user = User::factory()->create(['email' => 'zoe@example.com']);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'pas le bon']);
        }

        // Dix erreurs d'affilée, puis le bon mot de passe : on entre tout de suite.
        $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'password'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_robot_guessing_one_account_is_slowed_down(): void
    {
        User::factory()->create(['email' => 'zoe@example.com']);

        for ($i = 0; $i < 20; $i++) {
            $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'essai '.$i]);
        }

        // Après 20 essais ratés en 5 minutes, même le bon mot de passe doit attendre.
        $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
        $this->assertStringStartsWith('Trop d’essais', session('errors')->first('login'));
    }

    public function test_visitors_are_sent_to_the_login_page(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_an_unconfirmed_student_is_asked_to_check_their_mailbox(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_logged_in_users_skip_the_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/connexion')
            ->assertRedirect(route('home'));
    }

    public function test_the_legal_pages_are_public(): void
    {
        $this->get('/conditions-utilisation')->assertOk()->assertSee('Qui édite Récital');
        $this->get('/confidentialite')->assertOk()->assertSee('Tes droits');
    }
}
