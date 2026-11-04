<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Récital : chaque nuit, ménage des comptes jamais confirmés et des comptes inutilisés
| depuis 2 ans, comme promis dans la politique de confidentialité.
| Sur alwaysdata, une tâche planifiée lance « php artisan schedule:run » chaque minute,
| ou directement « php artisan recital:clean-accounts » une fois par jour (voir le README).
*/
Schedule::command('recital:clean-accounts')->dailyAt('03:30');
