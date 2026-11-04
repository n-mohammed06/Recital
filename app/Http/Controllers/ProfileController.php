<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use App\Support\ProgressSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Le profil de l'élève : ses informations, ses progrès et ses réglages.
 * Le changement de mot de passe est dans Auth\PasswordController.
 */
class ProfileController extends Controller
{
    /**
     * Page « Ton profil ».
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('profile.show', [
            'user' => $user,
            'progress' => ProgressSummary::for($user),
        ]);
    }

    /**
     * Formulaire « Tes informations » : prénom et nom.
     */
    public function editInformation(Request $request): View
    {
        return view('profile.information', ['user' => $request->user()]);
    }

    /**
     * Enregistre le prénom et le nom, avec les mêmes règles qu'à l'inscription.
     */
    public function updateInformation(Request $request): RedirectResponse
    {
        $data = $request->validate(RegisterController::nameRules(), RegisterController::NAME_MESSAGES);

        $request->user()->update($data);

        return redirect()->route('profile')->with('status', 'Tes informations sont enregistrées.');
    }

    /**
     * Page « Ton avatar » : les dessins proposés, ou les initiales.
     */
    public function editAvatar(Request $request): View
    {
        return view('profile.avatar', [
            'user' => $request->user(),
            'avatars' => config('recital.avatars'),
        ]);
    }

    /**
     * Enregistre l'avatar choisi. « Mes initiales » envoie une valeur vide.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'avatar' => ['present', 'nullable', 'string', Rule::in(array_keys(config('recital.avatars')))],
        ], [
            'avatar.present' => 'Choisis un avatar.',
            'avatar.string' => 'Choisis un des avatars proposés.',
            'avatar.in' => 'Choisis un des avatars proposés.',
        ]);

        $request->user()->update(['avatar' => $data['avatar']]);

        return redirect()->route('profile')->with('status', $data['avatar'] === null
            ? 'Ton profil affiche de nouveau tes initiales.'
            : 'Ton avatar est enregistré.');
    }

    /**
     * Enregistre les réglages : noms des notes et objectif du jour.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'note_names' => ['required', Rule::in(array_keys(User::NOTE_NAMES))],
            'daily_goal' => ['required', 'integer', Rule::in(User::DAILY_GOALS)],
        ], [
            'note_names.required' => 'Choisis comment afficher les noms des notes.',
            'note_names.in' => 'Choisis comment afficher les noms des notes.',
            'daily_goal.required' => 'Choisis ton objectif du jour.',
            'daily_goal.integer' => 'Choisis ton objectif du jour.',
            'daily_goal.in' => 'Choisis ton objectif du jour.',
        ]);

        $request->user()->update([
            'note_names' => $data['note_names'],
            'daily_goal' => (int) $data['daily_goal'],
        ]);

        // Retour sur la section des réglages, avec le message juste au-dessus du formulaire.
        return redirect()->to(route('profile').'#reglages')->with('settings_status', 'Tes réglages sont enregistrés.');
    }
}
