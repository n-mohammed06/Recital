<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avatar choisi dans le profil, parmi les dessins de config/recital.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nom du dessin, par exemple « chat-pianiste ». Vide : le profil montre les initiales.
            $table->string('avatar', 40)->nullable()->after('daily_goal');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
