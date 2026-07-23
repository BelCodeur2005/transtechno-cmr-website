<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    'breadcrumbs' => [
        'logs'            => true,
        'cache'           => true,
        'livewire'        => true,
        'sql_queries'     => true,
        'sql_bindings'    => false,
        'queue_info'      => true,
        'command_info'    => true,
        'http_client_requests' => true,
        'notifications'   => true,
    ],

    'tracing' => [
        'queue_job_transactions'   => env('SENTRY_TRACE_QUEUE_ENABLED', false),
        'queue_jobs'               => true,
        'sql_queries'              => true,
        'sql_origin'               => true,
        'views'                    => true,
        'livewire'                 => true,
        'http_client_requests'     => true,
        'redis_commands'           => env('SENTRY_TRACE_REDIS_COMMANDS', false),
        'redis_origin'             => true,
        'notifications'            => true,
        'cache'                    => false,
    ],

    'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),

    'traces_sample_rate' => (float) (env('SENTRY_TRACES_SAMPLE_RATE', 0.1)),

    'profiles_sample_rate' => (float) (env('SENTRY_PROFILES_SAMPLE_RATE', 0.0)),

    'ignore_exceptions' => [],

    'ignore_transactions' => [
        'GET /health',
        'GET /up',
    ],
];
