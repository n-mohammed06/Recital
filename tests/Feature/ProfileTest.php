<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        return User::factory()->create([
            'first_name' => 'Zoé',
            'last_name' => 'Martin',
            'email' => 'zoe@example.com',
        ]);
    }

    public function test_the_profile_shows_the_student_and_their_progress(): void
    {
        $this->actingAs($this->student())
            ->get('/profil')
            ->assertOk()
            ->assertSee('Zoé Martin')
            ->assertSee('zoe@example.com')
            ->assertSee('Niveau Découverte')
            ->assertSee('Tes progrès')
            ->assertSee('jour d’affilée')
            ->assertSee('Termine ta première leçon')
            ->assertSee('Do Ré Mi')
            ->assertSee('Se déconnecter');
    }

    public function test_the_home_page_leads_to_the_profile(): void
    {
        $this->actingAs($this->student())
            ->get('/')
            ->assertOk()
            ->assertSee(route('profile'));
    }

    public function test_visitors_and_unconfirmed_students_cannot_open_the_profile(): void
    {
        $this->get('/profil')->assertRedirect(route('login'));

        $this->actingAs(User::factory()->unverified()->create())
            ->get('/profil')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_a_student_can_change_their_first_and_last_name(): void
    {
        $user = $this->student();

        $this->actingAs($user)->get('/profil/informations')->assertOk()->assertSee('value="Zoé"', escape: false);

        $this->actingAs($user)
            ->put('/profil/informations', ['first_name' => 'Zoé-Lou', 'last_name' => 'N’Diaye'])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('Zoé-Lou', $user->first_name);
        $this->assertSame('N’Diaye', $user->last_name);
    }

    public function test_names_follow_the_same_rules_as_at_registration(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->put('/profil/informations', ['first_name' => '<script>', 'last_name' => ''])
            ->assertSessionHasErrors(['first_name', 'last_name']);

        $this->assertSame('Zoé', $user->fresh()->first_name);
    }

    public function test_a_new_student_starts_with_the_default_settings(): void
    {
        $user = $this->student()->fresh();

        $this->assertSame('do-re-mi', $user->note_names);
        $this->assertSame(10, $user->daily_goal);
    }

    public function test_a_student_can_change_their_settings(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->put('/profil/reglages', ['note_names' => 'c-d-e', 'daily_goal' => '15'])
            ->assertRedirect(route('profile').'#reglages')
            ->assertSessionHas('settings_status');

        $user->refresh();
        $this->assertSame('c-d-e', $user->note_names);
        $this->assertSame(15, $user->daily_goal);
    }

    public function test_settings_only_accept_the_proposed_choices(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->put('/profil/reglages', ['note_names' => 'do-re-fa', 'daily_goal' => '7'])
            ->assertSessionHasErrors(['note_names', 'daily_goal']);

        $user->refresh();
        $this->assertSame('do-re-mi', $user->note_names);
        $this->assertSame(10, $user->daily_goal);
    }
}
