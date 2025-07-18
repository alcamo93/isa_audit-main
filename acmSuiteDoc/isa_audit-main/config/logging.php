<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'obligations' => [
            'driver' => 'single',
            'path' => storage_path('logs/obligations.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'obligation_reminder' => [
            'driver' => 'single',
            'path' => storage_path('logs/obligation_reminder.log'),
            'level' => 'info',
            'bubble' => false
        ],
        
        'action_normal' => [
            'driver' => 'single',
            'path' => storage_path('logs/action_normal.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'action_expired' => [
            'driver' => 'single',
            'path' => storage_path('logs/action_expired.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'action_reminder' => [
            'driver' => 'single',
            'path' => storage_path('logs/action_reminder.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'file_notification_reminder' => [
            'driver' => 'single',
            'path' => storage_path('logs/file_notification_reminder.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'task_approve' => [
            'driver' => 'single',
            'path' => storage_path('logs/task_approve.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'task_normal' => [
            'driver' => 'single',
            'path' => storage_path('logs/task_normal.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'task_expired' => [
            'driver' => 'single',
            'path' => storage_path('logs/task_expired.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'task_reminder' => [
            'driver' => 'single',
            'path' => storage_path('logs/task_reminder.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'historical' => [
            'driver' => 'single',
            'path' => storage_path('logs/historical.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'transaction_obligation_action_plan' => [
            'driver' => 'single',
            'path' => storage_path('logs/transaction_obligation_action_plan.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'delete_evidence' => [
            'driver' => 'single',
            'path' => storage_path('logs/delete_evidence.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'contract_notification_reminder' => [
            'driver' => 'single',
            'path' => storage_path('logs/contract_notification_reminder.log'),
            'level' => 'info',
            'bubble' => false
        ],

        'backups' => [
            'driver' => 'single',
            'path' => storage_path('logs/backups.log'),
            'level' => 'info',
            'bubble' => false
        ],
    ],

];
