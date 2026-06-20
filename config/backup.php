<?php

return [

    'backup' => [
        'name' => env('APP_NAME', 'transtechno-cmr'),

        'source' => [
            'files' => [
                // Les fichiers médias sont déjà sur N0C Storage (S3) — pas besoin de les sauvegarder.
                // Le code source est sur GitHub.
                // On sauvegarde uniquement la base de données.
                'include' => [],
                'exclude' => [],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        // Compression gzip du dump SQL avant mise en archive.
        'database_dump_compressor' => \Spatie\DbDumper\Compressors\GzipCompressor::class,

        'database_dump_file_timestamp_format' => null,
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => '',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 6,
            'filename_prefix' => 'backup_',

            // Stockage sur N0C Storage (S3-compatible).
            'disks' => [
                's3',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        // Chiffrement de l'archive ZIP — définir BACKUP_ARCHIVE_PASSWORD dans .env
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',

        'tries' => 2,
        'retry_delay' => 60,
    ],

    'notifications' => [
        'notifications' => [
            // Alertes uniquement en cas d'échec — évite le spam d'emails quotidien.
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class          => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class  => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class         => ['mail'],
            // Succès silencieux (commenter la ligne suivante pour recevoir un email à chaque backup réussi)
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class      => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class    => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class     => [],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'transtechno.cm@gmail.com'),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'office@transtechno-cmr.com'),
                'name'    => env('MAIL_FROM_NAME', 'Transtechno Cameroun'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel'     => null,
            'username'    => null,
            'icon'        => null,
        ],
        'discord' => ['webhook_url' => '', 'username' => '', 'avatar_url' => ''],
    ],

    'monitor_backups' => [
        [
            'name'   => env('APP_NAME', 'transtechno-cmr'),
            'disks'  => ['s3'],
            'health_checks' => [
                // Alerte si le dernier backup a plus de 2 jours
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class         => 2,
                // Alerte si les backups dépassent 500 Mo sur S3
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 500,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days'                      => 7,   // tous les backups des 7 derniers jours
            'keep_daily_backups_for_days'                    => 30,  // 1 backup/jour pendant 30 jours
            'keep_weekly_backups_for_weeks'                  => 12,  // 1 backup/semaine pendant 3 mois
            'keep_monthly_backups_for_months'                => 12,  // 1 backup/mois pendant 1 an
            'keep_yearly_backups_for_years'                  => 2,   // 1 backup/an pendant 2 ans
            'delete_oldest_backups_when_using_more_megabytes_than' => 500,
        ],

        'tries'       => 2,
        'retry_delay' => 60,
    ],

];
