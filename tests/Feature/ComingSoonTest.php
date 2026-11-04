<?php

namespace Tests\Feature;

use App\Http\Middleware\ComingSoon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComingSoonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Le mode « Bientôt disponible », comme sur le serveur avant l'ouverture.
        config([
            'recital.coming_soon' => true,
            'recital.preview_code' => 'code-secret-de-test',
        ]);
    }

    public function test_the_public_only_sees_the_coming_soon_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Bientôt disponible')
            ->assertSee('Du premier Do à ton récital.');

        // Une simple visite ne dépose aucun cookie, pas même celui de session.
        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_every_other_page_leads_back_to_the_coming_soon_page(): void
    {
        foreach (['/connexion', '/inscription', '/mot-de-passe-oublie', '/profil', '/page-qui-n-existe-pas'] as $url) {
            $this->get($url)->assertRedirect('/');
        }
    }

    public function test_nobody_can_register_or_log_in_without_the_secret_link(): void
    {
        $user = User::factory()->create(['email' => 'zoe@example.com']);

        $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'password'])->assertRedirect('/');
        $this->assertGuest();

        $this->post('/inscription', [
            'first_name' => 'Léo',
            'last_name' => 'Martin',
            'email' => 'leo@example.com',
            'password' => 'gammes2026',
            'terms' => '1',
            'age' => '1',
        ])->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['email' => 'leo@example.com']);

        // Même un élève déjà connecté n'entre pas sans le lien.
        $this->actingAs($user)->get('/profil')->assertRedirect('/');
    }

    public function test_the_legal_pages_stay_open(): void
    {
        $this->get('/conditions-utilisation')->assertOk()->assertSee('Qui édite Récital');
        $this->get('/confidentialite')->assertOk()->assertSee('Tes droits');
    }

    public function test_the_secret_link_opens_the_site_on_this_device(): void
    {
        $this->get('/apercu/code-secret-de-test')
            ->assertRedirect(route('login'))
            ->assertCookie(ComingSoon::COOKIE, ComingSoon::cookieValue(), encrypted: false);

        $this->withUnencryptedCookie(ComingSoon::COOKIE, ComingSoon::cookieValue())
            ->get('/connexion')
            ->assertOk()
            ->assertSee('Se connecter');
    }

    public function test_a_wrong_code_opens_nothing(): void
    {
        $this->get('/apercu/mauvais-code')->assertNotFound()->assertCookieMissing(ComingSoon::COOKIE);

        // Un cookie inventé ne marche pas non plus.
        $this->withUnencryptedCookie(ComingSoon::COOKIE, 'cookie-invente')->get('/connexion')->assertRedirect('/');
    }

    public function test_without_a_code_nobody_can_enter(): void
    {
        config(['recital.preview_code' => null]);

        $this->get('/apercu/code-secret-de-test')->assertNotFound();
        $this->assertNull(ComingSoon::cookieValue());
    }

    public function test_changing_the_code_closes_the_preview_for_everyone(): void
    {
        $oldCookie = ComingSoon::cookieValue();

        config(['recital.preview_code' => 'nouveau-code']);

        $this->withUnencryptedCookie(ComingSoon::COOKIE, $oldCookie)->get('/connexion')->assertRedirect('/');
    }

    public function test_guessing_the_code_is_slowed_down(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->get('/apercu/essai-'.$i)->assertNotFound();
        }

        $this->get('/apercu/code-secret-de-test')->assertTooManyRequests();
    }

    public function test_the_site_is_open_as_usual_when_the_mode_is_off(): void
    {
        config(['recital.coming_soon' => false]);

        $this->get('/')->assertRedirect(route('login'));
        $this->get('/connexion')->assertOk();
    }
}
