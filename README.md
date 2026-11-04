# Récital : les comptes des élèves

Code pour **Laravel 13** (PHP 8.3 ou plus). Il gère tout ce qui touche aux comptes, prêt à être utilisé par des centaines d'élèves :

- **inscription** : prénom, nom, adresse e-mail, mot de passe (8 caractères minimum, une lettre, un chiffre, et refusé s'il est déjà apparu dans une fuite de données), case des conditions d'utilisation et case d'âge ;
- **confirmation de l'adresse e-mail** par un lien reçu à l'inscription : preuve que l'adresse est bien celle de l'élève ;
- **connexion**, avec le bouton « œil » pour afficher ou masquer le mot de passe et la case « Se souvenir de moi » ;
- **mot de passe oublié** : un lien par e-mail pour en choisir un nouveau ;
- **profil** : initiales, niveau, progrès (à zéro tant que les leçons n'existent pas), réglages (noms des notes Do Ré Mi ou C D E, objectif du jour de 5, 10 ou 15 minutes), modification du prénom et du nom ;
- **changement de mot de passe** depuis le profil : l'actuel est demandé, les autres appareils sont déconnectés et un e-mail de sécurité prévient l'élève ;
- **mode « Bientôt disponible »** : avant l'ouverture, le public ne voit qu'une page d'attente ; un lien secret ouvre tout le site pour toi, tes testeurs et les recruteurs ;
- **anti-robot invisible** : un élève qui se trompe n'attend jamais, seules les rafales impossibles pour un humain sont freinées ;
- **ménage automatique** : comptes jamais confirmés supprimés après 7 jours, comptes inutilisés depuis 2 ans supprimés après un e-mail d'avertissement ;
- **conditions d'utilisation** et **politique de confidentialité** ;
- pages d'erreur en français.

Les pages de compte gardent la charte Récital. Sur la connexion, l'inscription et la page « Bientôt disponible », une scène de théâtre ouvre la page : rideaux de velours, lambrequin à franges dorées, projecteur et piano à queue. Devant la scène, « Récital » en grand et le slogan qui s'écrit puis s'efface. En bas de ces pages, un clavier. Une fois connecté, l'élève navigue avec des onglets en bas de l'écran, à portée de pouce : Accueil et Profil (Parcours et Piano viendront avec les leçons).

## Ce que contient ce dossier

```
app/
  Console/Commands/CleanAccounts.php               ménage quotidien des comptes
  Http/Controllers/Auth/RegisterController.php     inscription
  Http/Controllers/Auth/LoginController.php        connexion, anti-robot, déconnexion
  Http/Controllers/Auth/EmailVerificationController.php   confirmation de l'adresse
  Http/Controllers/Auth/PasswordResetController.php       mot de passe oublié
  Http/Controllers/Auth/PasswordController.php     changer de mot de passe depuis le profil
  Http/Controllers/ProfileController.php           profil : informations, progrès, réglages
  Http/Controllers/PreviewAccessController.php     lien secret de l'aperçu privé
  Http/Controllers/LegalPageController.php         affiche les textes de resources/legal/
  Http/Middleware/ComingSoon.php                   mode « Bientôt disponible »
  Http/Middleware/SecurityHeaders.php              en-têtes de sécurité
  Http/Middleware/UseRealClientIp.php              vraie adresse IP derrière le proxy de l'hébergeur
  Listeners/RecordLastLogin.php                    date de dernière connexion
  Models/User.php                                  l'élève et ses réglages
  Notifications/InactiveAccountWarning.php         e-mail avant suppression d'un compte inactif
  Notifications/PasswordChanged.php                e-mail de sécurité après un changement de mot de passe
  Providers/AppServiceProvider.php                 règles des mots de passe, limites anti-robot
  Support/ProgressSummary.php                      les progrès affichés dans le profil
  Support/UserSessions.php                         déconnecte les autres appareils
bootstrap/app.php                                  proxy, en-têtes de sécurité, mode « Bientôt disponible »
config/auth.php                                    « Se souvenir de moi » limité à 1 an
config/recital.php                                 les phrases du slogan, le mode « Bientôt disponible »
config/trustedproxy.php                            réglages du proxy de l'hébergeur
database/                                          table des élèves et de leurs réglages, compte de test
lang/fr.json                                       e-mails en français
public/                                            styles, JavaScript, polices, icônes
resources/legal/                                   textes des conditions et de la politique
resources/views/                                   pages, composants, pages d'erreur
  coming-soon.blade.php                            page « Bientôt disponible »
  profile/                                         profil, informations, changer de mot de passe
routes/web.php                                     les adresses du site
routes/console.php                                 ménage programmé chaque nuit
tests/Feature/                                     61 tests automatiques
phpunit.xml                                        réglages des tests (toujours sur le site ouvert)
```

## Installation sur ton ordinateur

1. Crée un projet Laravel tout neuf :

   ```
   composer create-project laravel/laravel recital
   cd recital
   ```

2. Copie le contenu de ce dossier dans le projet et **remplace** les fichiers qui existent déjà (`bootstrap/app.php`, `routes/web.php`, `routes/console.php`, `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, `config/auth.php`, la migration des utilisateurs, `UserFactory.php`, `DatabaseSeeder.php`, `tests/Feature/ExampleTest.php`, `phpunit.xml`, `README.md`). Tu peux supprimer `resources/views/welcome.blade.php`.

3. Dans le fichier `.env`, change ces lignes :

   ```
   APP_NAME="Récital"
   APP_LOCALE=fr
   APP_FAKER_LOCALE=fr_FR
   MAIL_MAILER=log
   ```

   Avec `MAIL_MAILER=log`, les e-mails ne partent pas : ils sont écrits dans `storage/logs/laravel.log`. Tu y trouves le lien de confirmation et le lien « mot de passe oublié » à copier dans le navigateur.

4. Recrée la base de données (attention : cette commande **efface** tout ce qu'elle contient) :

   ```
   php artisan migrate:fresh --seed
   ```

5. Lance le site avec `php artisan serve` et ouvre http://localhost:8000. Compte de test : `test@example.com`, mot de passe `password`.

6. Pour le voir comme sur un téléphone : dans Chrome, F12 puis l'icône du téléphone. Ou sur ton téléphone, connecté au même Wi-Fi : `php artisan serve --host=0.0.0.0`, puis `http://ADRESSE-IP-DE-TON-ORDINATEUR:8000`.

7. Lance les tests : `php artisan test`.

**Voir la page « Bientôt disponible » sur ton ordinateur** : ajoute `RECITAL_COMING_SOON=true` et `RECITAL_PREVIEW_CODE=essai` dans le `.env`, ouvre http://localhost:8000 puis http://localhost:8000/apercu/essai. Retire ces deux lignes quand tu as fini. Les tests, eux, ne sont pas gênés : ils vérifient toujours le site ouvert.

**Mise à jour du code** : si tu avais déjà copié une version précédente, recopie tout le dossier par-dessus, puis lance `php artisan migrate` pour ajouter les nouvelles colonnes (les réglages du profil) sans effacer tes comptes d'essai.

**Si une vraie adresse est refusée** (« ne semble pas valide ») : la règle `email:rfc,dns` vérifie que le domaine existe, ce qui demande Internet. Hors connexion, remplace-la par `email:rfc` dans `RegisterController.php` le temps de tes essais.

## Mise en ligne chez alwaysdata

alwaysdata est un hébergeur français (serveurs en région parisienne). Son offre gratuite (1 Go, PHP, base de données, e-mails, HTTPS) suffit pour démarrer, mais elle est réservée à un usage personnel : si Récital devient payant, il faudra passer à une offre payante.

1. **Base de données** : dans **Bases de données > MySQL**, crée une base et un utilisateur. Pour des centaines d'élèves en même temps, MySQL tient mieux la charge que SQLite.
2. **Nom de domaine (conseillé)** : depuis un site hébergé chez alwaysdata, les e-mails partent sans mot de passe. Pour qu'ils n'arrivent pas dans les indésirables, le mieux est une adresse d'expédition sur ton propre nom de domaine (par exemple `bonjour@ton-domaine.fr`), ajouté dans **Domaines**, avec SPF et DKIM activés (voir la documentation d'alwaysdata). Sans domaine, fais un essai avec ta propre adresse et regarde où l'e-mail arrive.
3. **Code** : envoie le projet par SSH ou SFTP (sans `vendor/` ni `.env`), puis dans le dossier du projet :

   ```
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   ```

4. **Le `.env` du serveur** (remplace les valeurs entre crochets) :

   ```
   APP_NAME="Récital"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://[ton-adresse]
   APP_LOCALE=fr

   DB_CONNECTION=mysql
   DB_HOST=mysql-[compte].alwaysdata.net
   DB_DATABASE=[nom de la base]
   DB_USERNAME=[utilisateur]
   DB_PASSWORD=[mot de passe]

   SESSION_SECURE_COOKIE=true
   TRUSTED_PROXIES=*
   REAL_IP_HEADER=X-Real-IP
   LOG_STACK=daily

   MAIL_MAILER=smtp
   MAIL_SCHEME=smtp
   MAIL_HOST=smtp-[compte].alwaysdata.net
   MAIL_PORT=587
   MAIL_USERNAME=
   MAIL_PASSWORD=
   MAIL_FROM_ADDRESS=[adresse d'expédition]
   MAIL_FROM_NAME="Récital"

   RECITAL_COMING_SOON=true
   RECITAL_PREVIEW_CODE=[code secret]
   ```

   `APP_DEBUG=false` est indispensable : sinon une erreur afficherait des secrets à l'écran. `TRUSTED_PROXIES` et `REAL_IP_HEADER` servent parce que chaque visite passe d'abord par le proxy d'alwaysdata : sans eux, le site ne verrait pas qu'il est en HTTPS et croirait que tous les élèves ont la même adresse IP.

   **Mode « Bientôt disponible »** : avec `RECITAL_COMING_SOON=true`, le public ne voit que la page « Bientôt disponible » et les pages légales. Pour le code secret, prends 32 caractères au hasard, par exemple avec `php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"`. Ensuite, ouvre `https://[ton-adresse]/apercu/[code secret]` sur chaque appareil qui doit voir le vrai site (ton téléphone, celui d'un testeur, l'ordinateur d'un recruteur) : l'accès dure 90 jours. Pour fermer l'accès à tout le monde, change le code. Le jour de l'ouverture, mets `RECITAL_COMING_SOON=false`, puis relance `php artisan optimize`.

5. **Tables et optimisation** :

   ```
   php artisan migrate --force
   php artisan optimize
   ```

   Relance `php artisan optimize` après chaque mise à jour du code.

6. **Site** : dans **Web > Sites**, ajoute un site de type PHP dont le **répertoire racine** est le dossier `public` du projet. Seul ce dossier doit être visible : le reste (dont le `.env`) ne doit jamais être accessible depuis le web. Dans l'onglet **SSL**, coche **Forcer le HTTPS**.
7. **Ménage quotidien** : dans **Avancé > Tâches planifiées**, crée une tâche de type commande, une fois par jour, par exemple à 3 h 30 :

   ```
   cd ~/recital && php artisan recital:clean-accounts
   ```

8. **Vérifie** : ouvre ton adresse dans une fenêtre de navigation privée (tu dois voir « Bientôt disponible »), puis ouvre le lien secret. Inscris-toi avec ta vraie adresse, confirme-la avec le lien reçu, essaie « Mot de passe oublié » et le changement de mot de passe dans le profil. Dans **E-mails > Historique**, tu vois si les e-mails sont bien partis.

## Ce qui protège les comptes

| Protection | Comment |
|---|---|
| Mots de passe illisibles | hachés avec bcrypt (coût 12), jamais enregistrés en clair |
| Mots de passe faibles | 8 caractères, une lettre, un chiffre, et refus des mots de passe déjà piratés (Have I Been Pwned : seuls 5 caractères d'une empreinte partent du site) |
| Robots qui devinent | au-delà de 20 essais ratés en 5 minutes sur un compte, ou de 100 en 1 minute depuis une connexion, attente de quelques minutes. Un élève n'atteint jamais ces chiffres |
| Comptes créés en masse | 60 inscriptions par minute au plus depuis une même connexion |
| Adresse qui n'est pas la sienne | lien de confirmation signé, valable 60 minutes |
| Mot de passe oublié | lien à usage unique de 60 minutes ; ensuite, les autres appareils sont déconnectés |
| Changer de mot de passe | l'actuel est demandé (10 essais par minute au plus), les autres appareils sont déconnectés, un e-mail prévient l'élève |
| Site pas encore ouvert | mode « Bientôt disponible » : lien secret comparé en temps constant, 10 essais par minute au plus, cookie signé avec la clé de l'application |
| Qui est inscrit ? | messages identiques pour « mot de passe oublié » et réponses à durée fixe pour la connexion |
| Vol de session | nouvel identifiant de session à chaque connexion, cookies HTTPS uniquement, illisibles par JavaScript |
| Faux formulaires venus d'autres sites | jeton CSRF sur chaque formulaire |
| Scripts injectés, vol de clics | Content-Security-Policy stricte, X-Frame-Options, aucune ressource extérieure |
| Deux inscriptions identiques au même instant | la base refuse la seconde, avec le message habituel |

**Limite connue** : l'inscription indique qu'une adresse a déjà un compte, pour aider l'élève. Quelqu'un peut donc savoir si une adresse est inscrite. C'est un choix courant pour un site d'apprentissage ; on pourra le revoir plus tard.

**Recommandation de la CNIL** : pour un mot de passe de 8 caractères, son exemple demande 3 types de caractères (par exemple minuscule, majuscule et chiffre). Récital demande une lettre et un chiffre, et compense par le refus des mots de passe piratés et l'anti-robot. Pour suivre l'exemple à la lettre, ajoute `->mixedCase()` dans `AppServiceProvider.php` et une règle « une majuscule » dans la liste affichée.

## Tenir des centaines d'élèves en même temps

- Les sessions et le cache sont dans la base MySQL : chaque élève peut être servi par n'importe quel processus du serveur.
- Les index de la table des élèves (adresse e-mail unique) gardent les recherches rapides.
- `php artisan optimize` met la configuration, les routes et les vues en cache.
- La connexion et l'inscription demandent un calcul volontairement lourd (bcrypt), environ 0,2 seconde de processeur chacune. L'offre gratuite d'alwaysdata (un quart de processeur, 256 Mo de mémoire) convient pour tester et pour quelques dizaines d'élèves à la fois. Pour des centaines d'élèves connectés en même temps, l'offre Small (1 processeur, 1 Go de mémoire) est un minimum raisonnable.

## Avant d'ouvrir le site au public

1. **Remplace les textes entre crochets** dans `resources/legal/conditions-utilisation.md` et `resources/legal/politique-confidentialite.md` : `[Prénom Nom]`, `[adresse e-mail de contact]`, `[adresse du site]`.
2. **Hébergeur** : les textes indiquent alwaysdata. Si tu en choisis un autre, change son nom et son adresse dans les deux fichiers. Comme tu édites le site à titre non professionnel, la loi te permet de ne publier ni ton adresse ni ton téléphone : il suffit que l'hébergeur connaisse ton identité.
3. **Change la date** en haut des textes à chaque modification.

## Ce que les textes promettent

| Promesse | Où en est le code |
|---|---|
| Mots de passe hachés, anti-robot, mots de passe piratés refusés | fait |
| Adresse à confirmer, compte supprimé sans confirmation sous 7 jours | fait (`recital:clean-accounts`) |
| Compte inutilisé 2 ans supprimé, avec un e-mail un mois avant | fait (`recital:clean-accounts`) |
| Cookies indispensables seulement, polices sans Google | fait |
| « Se souvenir de moi » : 1 an au plus | fait |
| Modifier son prénom, son nom, son mot de passe et ses réglages dans le profil | fait |
| Supprimer un compte sur demande sous 30 jours | à la main pour l'instant, bouton dans le profil plus tard |
| Journaux gardés 1 an au plus | `LOG_STACK=daily` garde 14 jours |
| Prévenir 15 jours avant un changement important des conditions | par e-mail, le moment venu |
| Prévenir les élèves et la CNIL en cas de fuite de données | à faire si ça arrive |

Supprimer un compte à la main :

```
php artisan tinker
> App\Models\User::where('email', 'adresse@exemple.fr')->first()?->delete();
```

## Prochaines étapes conseillées

- **Compléter le profil** : changer d'adresse e-mail, télécharger ses données, supprimer son compte.
- **Brancher les vrais progrès** quand les leçons existeront : seule la méthode `ProgressSummary::for()` change, pas les pages.
- **Connexion avec Google**, quand tu le souhaites.
- **Page QR code** sur ordinateur, comme dans la maquette.

## Points à expliquer en entretien

- **Mots de passe** : hachage bcrypt, 8 caractères avec lettre et chiffre, refus des mots de passe déjà piratés grâce à l'API « k-anonymity » de Have I Been Pwned (seul un préfixe d'empreinte SHA-1 est envoyé).
- **Anti-robot sans gêner les humains** : limites par compte et par connexion, réglées loin au-dessus d'un usage normal, même pour une classe entière derrière le même Wi-Fi.
- **Pas de fuite d'information** : réponses à durée fixe (200 ms) pour la connexion, message identique pour « mot de passe oublié ».
- **Liens par e-mail** : URL signées et limitées dans le temps pour la confirmation, jetons hachés à usage unique pour la réinitialisation, déconnexion des autres appareils.
- **Changer de mot de passe** : règle `current_password`, nouveau jeton « Se souvenir de moi » (les autres téléphones sont déconnectés), nouvel identifiant de session, suppression des autres sessions en base, e-mail de sécurité.
- **Mode « Bientôt disponible »** : middleware global placé avant les sessions (aucun cookie pour le public), cookie d'accès signé en HMAC avec la clé de l'application et comparé avec `hash_equals`, accès révoqué en changeant le code.
- **Derrière un proxy** : `X-Forwarded-Proto` pour le HTTPS et `X-Real-IP` pour l'adresse du visiteur, sans faire confiance à ces en-têtes ailleurs.
- **Défense en profondeur** : CSP stricte, cookies sécurisés, CSRF, aucune ressource extérieure.
- **RGPD** : données minimales, durées de conservation tenues par une tâche automatique, politique de confidentialité alignée sur le code.
- **Accessibilité** : vrais libellés (flottants), erreurs écrites avec une icône, récapitulatif des erreurs, slogan animé caché aux lecteurs d'écran et figé si le téléphone limite les animations, réglages en vrais boutons radio (clavier, lecteurs d'écran, mode contraste élevé).
- **Tests** : 61 tests automatiques, lancés sur un vrai Laravel 13. Ceux du lien secret et du changement de mot de passe ont été vérifiés en cassant volontairement le code qu'ils protègent : ils échouent bien.
