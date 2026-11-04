<?php

/*
|--------------------------------------------------------------------------
| Récital : site placé derrière un proxy (alwaysdata)
|--------------------------------------------------------------------------
| Chez alwaysdata, chaque visite passe d'abord par un proxy de l'hébergeur, qui
| parle au site en HTTP et ajoute deux en-têtes : X-Forwarded-Proto (http ou https)
| et X-Real-IP (l'adresse IP du visiteur).
|
| Sur le serveur, dans le .env :
|     TRUSTED_PROXIES=*
|     REAL_IP_HEADER=X-Real-IP
|
| En local, laisse ces deux lignes vides : sinon un visiteur pourrait inventer ces en-têtes.
*/

return [

    // Proxies dont on croit l'en-tête X-Forwarded-Proto. « * » : celui qui nous appelle.
    'proxies' => env('TRUSTED_PROXIES'),

    // En-tête qui porte la vraie adresse IP du visiteur.
    'real_ip_header' => env('REAL_IP_HEADER'),

];
