<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    private function student(array $attributes = []): User
    {
        return User::factory()->create(['first_name' => 'Zoé', 'last_name' => 'Martin'] + $attributes);
    }

    public function test_a_new_student_shows_their_initials(): void
    {
        $user = $this->student();

        $this->assertNull($user->fresh()->avatar);

        $this->actingAs($user)
            ->get('/profil')
            ->assertOk()
            ->assertSee('>ZM</span>', escape: false)
            ->assertSee('Changer d’avatar')
            ->assertSee(route('profile.avatar'))
            ->assertDontSee('/avatars/');
    }

    public function test_the_avatar_page_offers_every_drawing_and_the_initials(): void
    {
        $page = $this->actingAs($this->student())->get('/profil/avatar')->assertOk();

        $page->assertSee('Mes initiales')
            ->assertSee('name="avatar" value="" checked', escape: false);

        foreach (config('recital.avatars') as $avatar => $name) {
            $page->assertSee($name)
                ->assertSee('value="'.$avatar.'"', escape: false)
                ->assertSee(asset('avatars/'.$avatar.'.svg'));
        }
    }

    public function test_a_student_can_choose_an_avatar(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->put('/profil/avatar', ['avatar' => 'chat-pianiste'])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('status', 'Ton avatar est enregistré.');

        $this->assertSame('chat-pianiste', $user->fresh()->avatar);

        $this->actingAs($user)
            ->get('/profil')
            ->assertSee(asset('avatars/chat-pianiste.svg'))
            ->assertDontSee('>ZM</span>', escape: false);

        $this->actingAs($user)
            ->get('/profil/avatar')
            ->assertSee('value="chat-pianiste" checked', escape: false);
    }

    public function test_a_student_can_go_back_to_their_initials(): void
    {
        $user = $this->student(['avatar' => 'escargot-cor']);

        $this->actingAs($user)
            ->put('/profil/avatar', ['avatar' => ''])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('status', 'Ton profil affiche de nouveau tes initiales.');

        $this->assertNull($user->fresh()->avatar);
    }

    public function test_only_the_proposed_avatars_are_accepted(): void
    {
        $user = $this->student(['avatar' => 'chat-pianiste']);

        foreach ([['avatar' => 'licorne'], ['avatar' => '../../.env'], ['avatar' => ['chat-pianiste']], []] as $input) {
            $this->actingAs($user)
                ->from('/profil/avatar')
                ->put('/profil/avatar', $input)
                ->assertRedirect('/profil/avatar')
                ->assertSessionHasErrors('avatar');
        }

        $this->assertSame('chat-pianiste', $user->fresh()->avatar);
    }

    public function test_an_avatar_removed_from_the_list_gives_back_the_initials(): void
    {
        // Un dessin retiré de config/recital.php après avoir été choisi.
        $user = $this->student(['avatar' => 'ancien-dessin']);

        $this->assertNull($user->chosenAvatar());
        $this->assertNull($user->avatarUrl());

        $this->actingAs($user)
            ->get('/profil')
            ->assertSee('>ZM</span>', escape: false)
            ->assertDontSee('ancien-dessin');
    }

    public function test_each_proposed_avatar_has_its_drawing_and_each_drawing_is_proposed(): void
    {
        $avatars = array_keys(config('recital.avatars'));
        $files = array_map(fn ($file) => basename($file, '.svg'), glob(public_path('avatars/*.svg')));

        sort($avatars);
        sort($files);
        $this->assertSame($avatars, $files);
        $this->assertGreaterThan(10, count($avatars));

        foreach ($files as $avatar) {
            $svg = file_get_contents(public_path('avatars/'.$avatar.'.svg'));

            $this->assertMatchesRegularExpression('/^<svg\b[^>]*\bviewBox="0 0 120 120"/', $svg, $avatar);
            // Un dessin, rien d'autre : ni script, ni image, police ou lien venus d'ailleurs.
            $this->assertStringNotContainsStringIgnoringCase('<script', $svg, $avatar);
            $this->assertStringNotContainsStringIgnoringCase('<image', $svg, $avatar);
            $this->assertStringNotContainsStringIgnoringCase('<foreignObject', $svg, $avatar);
            $this->assertDoesNotMatchRegularExpression('/\son[a-z]+\s*=\s*["\']/i', $svg, $avatar);
            $this->assertDoesNotMatchRegularExpression('/href\s*=\s*["\'](?!#)/i', $svg, $avatar);
            $this->assertDoesNotMatchRegularExpression('/url\(\s*+["\']?+(?!#)/i', $svg, $avatar);
        }
    }

    public function test_visitors_cannot_open_the_avatar_page(): void
    {
        $this->get('/profil/avatar')->assertRedirect(route('login'));
        $this->put('/profil/avatar', ['avatar' => 'chat-pianiste'])->assertRedirect(route('login'));
    }
}
