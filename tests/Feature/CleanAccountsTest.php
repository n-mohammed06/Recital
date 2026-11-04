<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\InactiveAccountWarning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CleanAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function test_an_address_never_confirmed_is_deleted_after_7_days(): void
    {
        $old = User::factory()->unverified()->create(['created_at' => now()->subDays(8)]);
        $recent = User::factory()->unverified()->create(['created_at' => now()->subDays(2)]);

        $this->artisan('recital:clean-accounts')->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
    }

    public function test_an_inactive_student_is_warned_one_month_before(): void
    {
        $inactive = User::factory()->create(['last_login_at' => now()->subMonths(23)->subDay()]);
        $active = User::factory()->create(['last_login_at' => now()->subWeek()]);

        $this->artisan('recital:clean-accounts')->assertSuccessful();

        Notification::assertSentTo($inactive, InactiveAccountWarning::class);
        Notification::assertNotSentTo($active, InactiveAccountWarning::class);
        $this->assertNotNull($inactive->fresh()->inactivity_warned_at);
        $this->assertModelExists($inactive);
    }

    public function test_a_warned_student_who_never_came_back_is_deleted(): void
    {
        $gone = User::factory()->create([
            'last_login_at' => now()->subYears(2)->subDay(),
            'inactivity_warned_at' => now()->subMonth()->subDay(),
        ]);

        $this->artisan('recital:clean-accounts')->assertSuccessful();

        $this->assertModelMissing($gone);
    }

    public function test_logging_in_cancels_the_warning(): void
    {
        $user = User::factory()->create([
            'email' => 'zoe@example.com',
            'last_login_at' => now()->subYears(2)->subDay(),
            'inactivity_warned_at' => now()->subMonth()->subDay(),
        ]);

        $this->post('/connexion', ['email' => 'zoe@example.com', 'password' => 'password']);
        $this->artisan('recital:clean-accounts')->assertSuccessful();

        $this->assertModelExists($user);
        $this->assertNull($user->fresh()->inactivity_warned_at);
    }
}
