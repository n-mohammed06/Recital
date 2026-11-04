<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Slogans
    |--------------------------------------------------------------------------
    | Les phrases qui s'écrivent puis s'effacent sous le nom « Récital », sur les pages
    | de connexion et d'inscription. La première est aussi celle qui reste affichée
    | quand les animations sont désactivées sur le téléphone.
    */

    'slogans' => [
        'Du premier Do à ton récital.',
        'Ta première mélodie ce soir.',
        'Le piano, une note après l’autre.',
        'Apprends le piano à ton rythme.',
        'Cinq minutes par jour suffisent.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mode « Bientôt disponible »
    |--------------------------------------------------------------------------
    | Tant que le site n'est pas ouvert au public, mets RECITAL_COMING_SOON=true dans le .env
    | du serveur : les visiteurs ne voient que la page « Bientôt disponible » et les pages légales.
    |
    | Le lien secret https://ton-adresse/apercu/CODE ouvre tout le site sur l'appareil qui l'ouvre,
    | pendant 90 jours : pour toi, tes testeurs et les recruteurs. CODE est la valeur de
    | RECITAL_PREVIEW_CODE (32 caractères au hasard). Change-la pour fermer l'accès à tout le monde.
    | Sans code, personne ne peut entrer.
    */

    'coming_soon' => (bool) env('RECITAL_COMING_SOON', false),

    'preview_code' => env('RECITAL_PREVIEW_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Avatars
    |--------------------------------------------------------------------------
    | Les dessins que l'élève peut choisir dans son profil, dans l'ordre de la grille.
    | À gauche : le nom du fichier dans public/avatars, sans « .svg ». À droite : le nom affiché.
    |
    | Ajouter un avatar : déposer son fichier SVG (rond, 120 x 120) dans public/avatars, puis ajouter sa ligne ici.
    | Retirer un avatar : supprimer sa ligne et son fichier. Les élèves qui l'avaient retrouvent leurs initiales.
    */

    'avatars' => [
        'chat-pianiste' => 'Chat pianiste',
        'renard-violoniste' => 'Renard violoniste',
        'pingouin-maestro' => 'Pingouin maestro',
        'lapin-flutiste' => 'Lapin flûtiste',
        'chouette-solfege' => 'Chouette solfège',
        'ours-violoncelle' => 'Ours au violoncelle',
        'herisson-clavier' => 'Hérisson clavier',
        'croche-rieuse' => 'Croche rieuse',
        'blanche-dormeuse' => 'Blanche dormeuse',
        'baleine-chanteuse' => 'Baleine chanteuse',
        'escargot-cor' => 'Escargot au cor',
        'croches-jumelles' => 'Croches jumelles',
        'elephant-trompette' => 'Éléphant trompette',
        'grenouille-tenor' => 'Grenouille ténor',
        'oiseau-croche' => 'Oiseau-croche',
        'pieuvre-orchestre' => 'Pieuvre homme-orchestre',
        'bourdon-virtuose' => 'Bourdon virtuose',
        'souris-metronome' => 'Souris au métronome',
    ],

];
