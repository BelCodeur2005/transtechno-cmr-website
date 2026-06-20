<?php

use Illuminate\Support\Facades\Schedule;

// ── Sauvegardes automatiques ────────────────────────────────────────────────
// Déclenchées par le cron cPanel : * * * * * php artisan schedule:run

Schedule::command('backup:clean')->dailyAt('01:00');          // Nettoyage des vieux backups
Schedule::command('backup:run --only-db')->dailyAt('02:00');  // Dump MySQL → S3
Schedule::command('backup:monitor')->dailyAt('03:00');        // Vérification santé backup
