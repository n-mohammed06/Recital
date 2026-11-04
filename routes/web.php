<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\PreviewAccessController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pages ouvertes à tout le monde
|--------------------------------------------------------------------------
| Elles restent ouvertes en mode « Bientôt disponible » (app/Http/Middleware/ComingSoon.php).
*/

Route::get('/conditions-utilisation', [LegalPageController::class, 'terms'])->name('legal.terms');
Route::get('/confidentialite', [LegalPageController::class, 'privacy'])->name('legal.privacy');

// Lien secret de l'aperçu privé, pendant le mode « Bientôt disponible ».
Route::get('/apercu/{code}', PreviewAccessController::class)->middleware('throttle:apercu')->name('preview');

/*
|--------------------------------------------------------------------------
| Inscription, connexion, mot de passe oublié : visiteurs non connectés
|--------------------------------------------------------------------------
| Un élève déjà connecté qui ouvre /connexion est renvoyé vers l'accueil.
| « throttle » : limites anti-robot, larges (voir AppServiceProvider).
*/

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [RegisterController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'store'])->middleware('throttle:inscription');

    // Laravel envoie les visiteurs non connectés vers la route nommée « login ».
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store']);

    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'store'])
        ->middleware('throttle:mot-de-passe')->name('password.email');

    // Adresse du lien reçu par e-mail (Laravel la construit avec le nom « password.reset »).
    Route::get('/nouveau-mot-de-passe/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/nouveau-mot-de-passe', [PasswordResetController::class, 'update'])
        ->middleware('throttle:mot-de-passe')->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Élèves connectés
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Confirmation de l'adresse e-mail. « signed » : le lien n'est valable que s'il n'a pas été modifié
    // et pendant 60 minutes. « throttle:6,1 » : 6 essais par minute au plus.
    Route::get('/verification', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verification/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/verification/renvoyer', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');

    // Pages réservées aux élèves dont l'adresse est confirmée.
    Route::middleware('verified')->group(function () {
        Route::view('/', 'home')->name('home');

        Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
        Route::get('/profil/informations', [ProfileController::class, 'editInformation'])->name('profile.information');
        Route::put('/profil/informations', [ProfileController::class, 'updateInformation'])->name('profile.information.update');
        Route::put('/profil/reglages', [ProfileController::class, 'updateSettings'])->name('profile.settings');
        Route::get('/profil/avatar', [ProfileController::class, 'editAvatar'])->name('profile.avatar');
        Route::put('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

        Route::get('/profil/mot-de-passe', [PasswordController::class, 'edit'])->name('profile.password');
        Route::put('/profil/mot-de-passe', [PasswordController::class, 'update'])
            ->middleware('throttle:changer-mot-de-passe')->name('profile.password.update');
    });
});
