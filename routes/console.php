<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 |--------------------------------------------------------------------------
 | Papelera Automática de Tickets
 |--------------------------------------------------------------------------
 | Se ejecuta diariamente a las 02:00 AM.
 | - Archiva tickets cerrados hace 4+ meses.
 | - Elimina permanentemente tickets en papelera hace 5+ días.
 |
 | En Hostinger se requiere un cron externo:
 |   * * * * * cd /home/u117731215/domains/tecnotunes.com.mx/app
 |            && php artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('tickets:archivar')->dailyAt('02:00');
