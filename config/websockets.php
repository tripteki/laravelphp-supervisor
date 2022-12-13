<?php

return [

    "apps" => [

        [
            "name" => env("APP_NAME"),
            "id" => env("WEBSOCKET_ID"),
            "key" => env("WEBSOCKET_KEY"),
            "secret" => env("WEBSOCKET_SECRET"),
            "path" => env("WEBSOCKET_PATH"),
            "host" => env("WEBSOCKET_HOST", "127.0.0.1"),
            "enable_client_messages" => false,
            "enable_statistics" => true,
        ],
    ],

    "ssl" => [

        "local_pk" => env("WEBSOCKET_SSL_LOCAL_PK", null),
        "local_cert" => env("WEBSOCKET_SSL_LOCAL_CERT", null),
        "capath" => env("WEBSOCKET_SSL_CA", null),
        "passphrase" => env("WEBSOCKET_SSL_PASSPHRASE", null),
        "verify_peer" => env("APP_ENV") === "production",
        "allow_self_signed" => env("APP_ENV") !== "production",
    ],

    "dashboard" => [

        "domain" => env("WEBSOCKET_DASHBOARD_DOMAIN"),
        "path" => env("WEBSOCKET_DASHBOARD_PATH", "websocket"),
        "port" => env("WEBSOCKET_PORT", 6001),

        "middleware" => [

            "web",
            // \BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize::class, //
        ],
    ],

    "managers" => [

        "app" => \BeyondCode\LaravelWebSockets\Apps\ConfigAppManager::class,

        "sqlite" => [

            "database" => env("DB_DATABASE", database_path("websocket.sqlite")),
        ],

        "mysql" => [

            "connection" => env("DB_CONNECTION", "mysql"),
            "table" => "websocket_apps",
        ],
    ],

    "statistics" => [

        "store" => \BeyondCode\LaravelWebSockets\Statistics\Stores\DatabaseStore::class,
        "interval_in_seconds" => 60,
        "delete_statistics_older_than_days" => 60,
    ],

    "replication" => [

        "mode" => env("WEBSOCKET_REPLICATION_MODE", "local"),

        "modes" => [

            "local" => [

                "channel_manager" => \BeyondCode\LaravelWebSockets\ChannelManagers\LocalChannelManager::class,
                "collector" => \BeyondCode\LaravelWebSockets\Statistics\Collectors\MemoryCollector::class,
            ],

            "redis" => [

                "connection" => env("WEBSOCKET_REDIS_REPLICATION_CONNECTION", "broadcasting"),
                "channel_manager" => \BeyondCode\LaravelWebSockets\ChannelManagers\RedisChannelManager::class,
                "collector" => \BeyondCode\LaravelWebSockets\Statistics\Collectors\RedisCollector::class,
            ],
        ],
    ],

    "handlers" => [

        "websocket" => \BeyondCode\LaravelWebSockets\Server\WebSocketHandler::class,
        "health" => \BeyondCode\LaravelWebSockets\Server\HealthHandler::class,
        "trigger_event" => \BeyondCode\LaravelWebSockets\API\TriggerEvent::class,
        "fetch_users" => \BeyondCode\LaravelWebSockets\API\FetchUsers::class,
        "fetch_channel" => \BeyondCode\LaravelWebSockets\API\FetchChannel::class,
        "fetch_channels" => \BeyondCode\LaravelWebSockets\API\FetchChannels::class,
    ],

    "max_request_size_in_kb" => 250,

    "promise_resolver" => \React\Promise\FulfilledPromise::class,

];
