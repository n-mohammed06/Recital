<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Un élève de test : test@example.com, mot de passe « password ».
        User::factory()->create([
            'first_name' => 'Zoé',
            'last_name' => 'Test',
            'email' => 'test@example.com',
        ]);
    }
}
