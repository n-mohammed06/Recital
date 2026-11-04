<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Réglages de l'élève, modifiables dans son profil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Noms des notes : « do-re-mi » (Do Ré Mi) ou « c-d-e » (C D E).
            $table->string('note_names', 10)->default('do-re-mi')->after('last_name');
            // Objectif du jour, en minutes de pratique : 5, 10 ou 15.
            $table->unsignedTinyInteger('daily_goal')->default(10)->after('note_names');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['note_names', 'daily_goal']);
        });
    }
};
