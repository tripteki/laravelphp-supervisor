<?php

return [

    "state_file" => storage_path("logs/supervisor.json"),

    "server" => [

        "command:development" => [

            "octane:start",
            "--host=".env("SERVER_HOST", "0.0.0.0"),
            "--watch",
            "--workers=auto",
            "--task-workers=auto",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "command:production" => [

            "octane:start",
            "--host=".env("SERVER_HOST", "0.0.0.0"),
            "--workers=auto",
            "--task-workers=auto",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "process" => 1,
        "increment" => [ "SERVER_PORT" => env("SERVER_PORT", "8000"), ],
        "stdout" => storage_path("logs/supervisor-server-stdout.log"),
        "stderr" => storage_path("logs/supervisor-server-stderr.log"),
    ],

    "websockets" => [

        "command" => [

            "websockets:serve",
            "--host=".env("WEBSOCKET_HOST", "127.0.0.1"),
            "--port=".env("WEBSOCKET_PORT", "6001"),
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "process" => 1,
        "stdout" => storage_path("logs/supervisor-websockets-stdout.log"),
        "stderr" => storage_path("logs/supervisor-websockets-stderr.log"),
    ],

    "ssr" => [

        "interpreter" => "node",

        "command" => [

            base_path("bootstrap/ssr/ssr.mjs"),
        ],

        "process" => 1,
        "stdout" => storage_path("logs/supervisor-ssr-stdout.log"),
        "stderr" => storage_path("logs/supervisor-ssr-stderr.log"),
    ],

    "schedule" => [

        "command" => [

            "schedule:work",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "process" => 2,
        "stdout" => storage_path("logs/supervisor-schedule-stdout.log"),
        "stderr" => storage_path("logs/supervisor-schedule-stderr.log"),
    ],

    "queue" => [

        "command" => [

            "queue:work",
            "--queue=high,low",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "process" => 2,
        "stdout" => storage_path("logs/supervisor-queue-stdout.log"),
        "stderr" => storage_path("logs/supervisor-queue-stderr.log"),
    ],

];
