<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisterController extends Controller
{
    /**
     * Lettres (accents compris), espaces, traits d'union et apostrophes :
     * « Jean-Luc », « Zoé », « N'Diaye », « de la Fontaine ».
     */
    private const NAME_PATTERN = '/^[\pL\pM\s\'’-]+$/u';

    /**
     * Messages des règles du mot de passe (fixées dans AppServiceProvider),
     * repris par les pages « Nouveau mot de passe » et « Changer de mot de passe ».
     */
    public const PASSWORD_MESSAGES = [
        'password.required' => 'Choisis un mot de passe.',
        'password.min' => 'Ton mot de passe doit faire au moins 8 caractères.',
        'password.max' => 'Ton mot de passe doit faire 64 caractères au maximum.',
        'password.letters' => 'Ton mot de passe doit contenir au moins une lettre.',
        'password.numbers' => 'Ton mot de passe doit contenir au moins un chiffre.',
        'password.uncompromised' => 'Ce mot de passe est déjà apparu dans une fuite de données sur un autre site. Choisis-en un autre.',
    ];

    /**
     * Messages des règles du prénom et du nom, repris par le profil.
     */
    public const NAME_MESSAGES = [
        'first_name.required' => 'Indique ton prénom.',
        'first_name.max' => 'Ton prénom doit faire 60 caractères au maximum.',
        'first_name.regex' => 'Ton prénom ne peut contenir que des lettres, des espaces, des traits d’union et des apostrophes.',
        'last_name.required' => 'Indique ton nom.',
        'last_name.max' => 'Ton nom doit faire 60 caractères au maximum.',
        'last_name.regex' => 'Ton nom ne peut contenir que des lettres, des espaces, des traits d’union et des apostrophes.',
    ];

    private const MESSAGES = [
        'email.required' => 'Indique ton adresse e-mail.',
        'email.max' => 'Ton adresse e-mail est trop longue.',
        'email.email' => 'Cette adresse e-mail ne semble pas valide. Vérifie-la.',
        'email.unique' => 'Un compte existe déjà avec cette adresse. Connecte-toi plutôt.',
        'terms.accepted' => 'Coche cette case pour accepter les conditions d’utilisation.',
        'age.accepted' => 'Coche cette case pour confirmer ton âge ou l’accord d’un parent.',
    ];

    /**
     * Règles du prénom et du nom, communes à l'inscription et au profil.
     *
     * @return array<string, array<int, string>>
     */
    public static function nameRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:60', 'regex:'.self::NAME_PATTERN],
            'last_name' => ['required', 'string', 'max:60', 'regex:'.self::NAME_PATTERN],
        ];
    }

    /**
     * Affiche le formulaire d'inscription.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Vérifie le formulaire, crée le compte, envoie le lien de confirmation et connecte l'élève.
     */
    public function store(Request $request): RedirectResponse
    {
        // L'adresse est enregistrée en minuscules : « Zoe@Mail.fr » et « zoe@mail.fr » sont le même compte.
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate(self::nameRules() + [
            // « dns » vérifie que le domaine de l'adresse existe vraiment (zoe@gmial.con est refusée).
            'email' => ['required', 'string', 'max:255', 'email:rfc,dns', 'unique:users,email'],
            // 8 caractères minimum, une lettre, un chiffre, pas un mot de passe piraté : voir AppServiceProvider.
            'password' => ['required', Password::defaults()],
            'terms' => ['accepted'],
            'age' => ['accepted'],
        ], self::NAME_MESSAGES + self::MESSAGES + self::PASSWORD_MESSAGES);

        $user = new User;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->terms_accepted_at = now();

        try {
            $user->save();
        } catch (UniqueConstraintViolationException) {
            // Deux inscriptions avec la même adresse au même instant : la base refuse la seconde.
            throw ValidationException::withMessages(['email' => self::MESSAGES['email.unique']]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Envoie l'e-mail avec le lien de confirmation (User implémente MustVerifyEmail).
        // Si l'envoi échoue un instant, le compte existe quand même : le bouton « Renvoyer l'e-mail » prendra le relais.
        try {
            event(new Registered($user));
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('verification.notice');
    }
}
