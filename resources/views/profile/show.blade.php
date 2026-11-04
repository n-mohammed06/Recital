{{-- Page « Ton profil » : informations, progrès et réglages de l'élève (ProfileController). --}}
@extends('layouts.recital')

@section('title', 'Ton profil')

@section('content')
    <x-app-page current="profile">
        @if (session('status'))
            <x-alert :title="session('status')" />
        @endif

        <header class="dg-profil">
            <span class="dg-avatar" aria-hidden="true">{{ $user->initials() }}</span>
            <div class="dg-profil__texte">
                <p class="dg-surtitre">Niveau {{ $progress->level }}</p>
                <h1 class="dg-titre-1"><span class="dg-visuellement-cache">Profil de </span>{{ $user->fullName() }}</h1>
                <p class="dg-texte-doux">Élève depuis {{ $user->memberSince() }}</p>
            </div>
        </header>

        {{-- Les chiffres viennent de App\Support\ProgressSummary : à zéro tant que les leçons n'existent pas. --}}
        <section class="dg-carte" aria-labelledby="titre-progres">
            <h2 class="dg-carte__titre" id="titre-progres">Tes progrès</h2>
            <ul class="dg-stats">
                <li class="dg-stat">
                    <b class="dg-stat__nombre">{{ $progress->streakDays }}</b>
                    <span>{{ $progress->streakDays > 1 ? 'jours' : 'jour' }} d’affilée</span>
                </li>
                <li class="dg-stat">
                    <b class="dg-stat__nombre">{{ $progress->lessonsCompleted }}</b>
                    <span>{{ $progress->lessonsCompleted > 1 ? 'leçons terminées' : 'leçon terminée' }}</span>
                </li>
                <li class="dg-stat">
                    <b class="dg-stat__nombre">{{ $progress->stars }}</b>
                    <span>{{ $progress->stars > 1 ? 'étoiles gagnées' : 'étoile gagnée' }}</span>
                </li>
                <li class="dg-stat">
                    <b class="dg-stat__nombre">{{ $progress->practiceMinutes }}</b>
                    <span>{{ $progress->practiceMinutes > 1 ? 'minutes de pratique' : 'minute de pratique' }}</span>
                </li>
            </ul>
            @if ($progress->lessonsCompleted === 0)
                <p class="dg-mention">Termine ta première leçon pour voir tes progrès ici.</p>
            @endif
        </section>

        <section class="dg-carte" id="reglages" aria-labelledby="titre-reglages">
            <h2 class="dg-carte__titre" id="titre-reglages">Réglages</h2>

            @if (session('settings_status'))
                <x-alert :title="session('settings_status')" />
            @endif

            @if ($errors->hasAny(['note_names', 'daily_goal']))
                <x-alert type="erreur" :title="$errors->first('note_names') ?: $errors->first('daily_goal')" />
            @endif

            <form class="dg-formulaire" method="POST" action="{{ route('profile.settings') }}" data-form>
                @csrf
                @method('PUT')

                <fieldset class="dg-reglage" aria-describedby="aide-notes">
                    <legend class="dg-reglage__nom">Noms des notes</legend>
                    <p class="dg-reglage__aide" id="aide-notes">Sur le clavier et dans les leçons.</p>
                    <div class="dg-segments">
                        @foreach (\App\Models\User::NOTE_NAMES as $value => $label)
                            <label class="dg-segment">
                                <input class="dg-segment__input" type="radio" name="note_names" value="{{ $value }}" @checked(old('note_names', $user->note_names) === $value)>
                                <span class="dg-segment__texte">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="dg-reglage" aria-describedby="aide-objectif">
                    <legend class="dg-reglage__nom">Objectif du jour</legend>
                    <p class="dg-reglage__aide" id="aide-objectif">Le temps de pratique que tu vises chaque jour.</p>
                    <div class="dg-segments">
                        @foreach (\App\Models\User::DAILY_GOALS as $minutes)
                            <label class="dg-segment">
                                <input class="dg-segment__input" type="radio" name="daily_goal" value="{{ $minutes }}" @checked((int) old('daily_goal', $user->daily_goal) === $minutes)>
                                <span class="dg-segment__texte"><span aria-hidden="true">{{ $minutes }} min</span><span class="dg-visuellement-cache">{{ $minutes }} minutes</span></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <button class="dg-bouton dg-bouton--secondaire" type="submit" data-submit data-sending-label="Enregistrement…">
                    <span class="dg-bouton__texte">Enregistrer les réglages</span>
                </button>
            </form>
        </section>

        <section class="dg-carte" aria-labelledby="titre-compte">
            <h2 class="dg-carte__titre" id="titre-compte">Ton compte</h2>
            <dl class="dg-infos">
                <div>
                    <dt>Adresse e-mail</dt>
                    <dd>{{ $user->email }}</dd>
                </div>
            </dl>
            <ul class="dg-menu">
                <li>
                    <a class="dg-menu__lien" href="{{ route('profile.information') }}">
                        Modifier mon prénom et mon nom
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </li>
                <li>
                    <a class="dg-menu__lien" href="{{ route('profile.password') }}">
                        Changer mon mot de passe
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </li>
            </ul>
        </section>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dg-bouton dg-bouton--secondaire dg-bouton--large" type="submit">Se déconnecter</button>
        </form>

        <x-legal-links />
    </x-app-page>
@endsection
