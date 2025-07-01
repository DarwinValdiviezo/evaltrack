<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Monitoreo
    |--------------------------------------------------------------------------
    |
    | Configuración para el sistema de monitoreo de EvalTrack
    |
    */

    // Configuración de health checks
    'health_checks' => [
        'database' => [
            'enabled' => env('HEALTH_CHECK_DATABASE', true),
            'timeout' => env('HEALTH_CHECK_DATABASE_TIMEOUT', 5),
        ],
        'cache' => [
            'enabled' => env('HEALTH_CHECK_CACHE', true),
            'timeout' => env('HEALTH_CHECK_CACHE_TIMEOUT', 3),
        ],
        'queue' => [
            'enabled' => env('HEALTH_CHECK_QUEUE', true),
            'timeout' => env('HEALTH_CHECK_QUEUE_TIMEOUT', 5),
        ],
        'storage' => [
            'enabled' => env('HEALTH_CHECK_STORAGE', true),
            'min_free_space' => env('HEALTH_CHECK_MIN_FREE_SPACE', 1024), // MB
        ],
    ],

    // Configuración de alertas
    'alerts' => [
        'email' => [
            'enabled' => env('ALERT_EMAIL_ENABLED', false),
            'recipients' => explode(',', env('ALERT_EMAIL_RECIPIENTS', 'admin@company.com')),
        ],
        'slack' => [
            'enabled' => env('ALERT_SLACK_ENABLED', false),
            'webhook_url' => env('ALERT_SLACK_WEBHOOK'),
            'channel' => env('ALERT_SLACK_CHANNEL', '#alerts'),
        ],
        'discord' => [
            'enabled' => env('ALERT_DISCORD_ENABLED', false),
            'webhook_url' => env('ALERT_DISCORD_WEBHOOK'),
        ],
    ],

    // Configuración de métricas
    'metrics' => [
        'enabled' => env('METRICS_ENABLED', true),
        'collectors' => [
            'database_queries' => env('METRICS_DB_QUERIES', true),
            'response_times' => env('METRICS_RESPONSE_TIMES', true),
            'memory_usage' => env('METRICS_MEMORY_USAGE', true),
            'error_rates' => env('METRICS_ERROR_RATES', true),
        ],
        'retention_days' => env('METRICS_RETENTION_DAYS', 30),
    ],

    // Configuración de logs
    'logs' => [
        'monitoring' => [
            'enabled' => env('MONITORING_LOGS_ENABLED', true),
            'level' => env('MONITORING_LOGS_LEVEL', 'info'),
            'retention_days' => env('MONITORING_LOGS_RETENTION', 90),
        ],
        'performance' => [
            'enabled' => env('PERFORMANCE_LOGS_ENABLED', true),
            'threshold_ms' => env('PERFORMANCE_THRESHOLD_MS', 1000),
        ],
    ],

    // Configuración de thresholds
    'thresholds' => [
        'response_time' => [
            'warning' => env('THRESHOLD_RESPONSE_TIME_WARNING', 1000), // ms
            'critical' => env('THRESHOLD_RESPONSE_TIME_CRITICAL', 3000), // ms
        ],
        'memory_usage' => [
            'warning' => env('THRESHOLD_MEMORY_WARNING', 80), // %
            'critical' => env('THRESHOLD_MEMORY_CRITICAL', 90), // %
        ],
        'disk_usage' => [
            'warning' => env('THRESHOLD_DISK_WARNING', 80), // %
            'critical' => env('THRESHOLD_DISK_CRITICAL', 90), // %
        ],
        'error_rate' => [
            'warning' => env('THRESHOLD_ERROR_RATE_WARNING', 5), // %
            'critical' => env('THRESHOLD_ERROR_RATE_CRITICAL', 10), // %
        ],
    ],

    // Configuración de endpoints de monitoreo
    'endpoints' => [
        'health' => '/health',
        'metrics' => '/metrics',
        'status' => '/status',
        'ready' => '/ready',
        'live' => '/live',
    ],

    // Configuración de servicios externos
    'external_services' => [
        'database' => [
            'postgres' => [
                'host' => env('DB_PGSQL_HOST', 'localhost'),
                'port' => env('DB_PGSQL_PORT', 5432),
                'database' => env('DB_PGSQL_DATABASE', 'evaltrack_users'),
                'timeout' => env('DB_PGSQL_TIMEOUT', 5),
            ],
            'mysql' => [
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', 3306),
                'database' => env('DB_DATABASE', 'evaltrack_business'),
                'timeout' => env('DB_TIMEOUT', 5),
            ],
        ],
        'cache' => [
            'redis' => [
                'host' => env('REDIS_HOST', 'localhost'),
                'port' => env('REDIS_PORT', 6379),
                'timeout' => env('REDIS_TIMEOUT', 3),
            ],
        ],
    ],

    // Configuración de notificaciones
    'notifications' => [
        'channels' => [
            'mail' => \App\Notifications\MonitoringAlertMail::class,
            'slack' => \App\Notifications\MonitoringAlertSlack::class,
            'discord' => \App\Notifications\MonitoringAlertDiscord::class,
        ],
        'cooldown_minutes' => env('NOTIFICATION_COOLDOWN_MINUTES', 15),
    ],

    // Configuración de reportes
    'reports' => [
        'daily' => [
            'enabled' => env('DAILY_REPORTS_ENABLED', true),
            'time' => env('DAILY_REPORTS_TIME', '08:00'),
            'recipients' => explode(',', env('DAILY_REPORTS_RECIPIENTS', 'admin@company.com')),
        ],
        'weekly' => [
            'enabled' => env('WEEKLY_REPORTS_ENABLED', true),
            'day' => env('WEEKLY_REPORTS_DAY', 'monday'),
            'time' => env('WEEKLY_REPORTS_TIME', '09:00'),
            'recipients' => explode(',', env('WEEKLY_REPORTS_RECIPIENTS', 'admin@company.com')),
        ],
    ],
]; 