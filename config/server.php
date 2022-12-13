<?php

use Laravel\Octane\Octane;
use Laravel\Octane\Contracts\OperationTerminated;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Listeners\CollectGarbage;
use Laravel\Octane\Listeners\DisconnectFromDatabases;
use Laravel\Octane\Listeners\EnsureUploadedFilesAreValid;
use Laravel\Octane\Listeners\EnsureUploadedFilesCanBeMoved;
use Laravel\Octane\Listeners\FlushTemporaryContainerInstances;
use Laravel\Octane\Listeners\FlushUploadedFiles;
use Laravel\Octane\Listeners\ReportException;
use Laravel\Octane\Listeners\StopWorkerIfNecessary;

return [

    "host" => env("SERVER_HOST", "127.0.0.1"),
    "port" => env("SERVER_PORT", "8000"),
    "https" => env("SERVER_HTTPS", false),

    "state_file" => storage_path("logs/server-state.json"),

    "swoole" => [

        "options" => [

            "pid_file" => storage_path("logs/server/server.pid"),
            "log_file" => storage_path("logs/server.log"),

            // "ssl_key_file" => ".key", //
            // "ssl_cert_file" => ".cert", //
        ],

        "ssl" => env("SERVER_HTTPS", false),
    ],



    "cache" => [

        "rows" => 1000,
        "bytes" => 10000,
    ],

    "tables" => [

        //
    ],

    "watch" => [

        "bin",
        "src",
        "app",
        "bootstrap",
        "config",
        "database",
        "public/**/*.php",
        "resources/**/*.php",
        "routes",
        "composer.lock",
        ".env",
    ],



    "listeners" => [

        WorkerStarting::class => [

            EnsureUploadedFilesAreValid::class,
            EnsureUploadedFilesCanBeMoved::class,
        ],

        RequestReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
            ...Octane::prepareApplicationForNextRequest(),
        ],

        RequestHandled::class => [

            //
        ],

        RequestTerminated::class => [

            // FlushUploadedFiles::class, //
        ],

        TaskReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
        ],

        TaskTerminated::class => [

            //
        ],

        TickReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
        ],

        TickTerminated::class => [

            //
        ],

        OperationTerminated::class => [

            FlushTemporaryContainerInstances::class,
            // DisconnectFromDatabases::class, //
            // CollectGarbage::class, //
        ],

        WorkerErrorOccurred::class => [

            ReportException::class,
            StopWorkerIfNecessary::class,
        ],

        WorkerStopping::class => [

            //
        ],
    ],



    "warm" => [

        ...Octane::defaultServicesToWarm(),
    ],

    "flush" => [

        //
    ],



    "garbage" => 50,
    "max_execution_time" => 30,
    "server" => "swoole",

];
