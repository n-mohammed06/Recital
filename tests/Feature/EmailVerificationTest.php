<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function verificationUrl(User $user, ?string $email = null): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($email ?? $user->email),
        ]);
    }

    public function test_the_check_your_mailbox_page_is_displayed(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'zoe@example.com']);

        $this->actingAs($user)
            ->get('/verification')
            ->assertOk()
            ->assertSee('Vérifie ta boîte mail')
            ->assertSee('zoe@example.com');
    }

    public function test_the_link_confirms_the_address(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get($this->verificationUrl($user))
            ->assertRedirect(route('home'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_a_link_for_another_address_is_refused(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get($this->verificationUrl($user, 'autre@example.com'))
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_a_modified_link_is_refused(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get($this->verificationUrl($user).'x')
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_the_email_can_be_sent_again(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->from('/verification')
            ->post('/verification/renvoyer')
            ->assertRedirect('/verification')
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_a_confirmed_student_goes_straight_home(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/verification')
            ->assertRedirect(route('home'));
    }
}
