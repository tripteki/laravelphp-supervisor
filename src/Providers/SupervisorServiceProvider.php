<?php

namespace Tripteki\Supervisor\Providers;

use Tripteki\Supervisor\Console\Commands\SupervisorStartCommand;
use Tripteki\Supervisor\Console\Commands\SupervisorReloadCommand;
use Tripteki\Supervisor\Console\Commands\SupervisorStopCommand;
use Tripteki\Supervisor\Console\Commands\SupervisorStatusCommand;
use Tripteki\Supervisor\Console\Commands\SupervisorStartupCommand;
use Illuminate\Support\ServiceProvider;

class SupervisorServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * @return bool
     */
    public static function shouldRunMigrations()
    {
        return static::$runsMigrations;
    }

    /**
     * @return void
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerPublishers();
        $this->registerCommands();
        $this->registerMigrations();
    }

    /**
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__."/../../config/supervisor.php", "supervisor");
    }

    /**
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole() && static::shouldRunMigrations()) {

            $this->loadMigrationsFrom(__DIR__."/../../../../beyondcode/laravel-websockets/database/migrations");
        }
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {

            $this->commands(
            [
                SupervisorStartCommand::class,
                SupervisorReloadCommand::class,
                SupervisorStopCommand::class,
                SupervisorStatusCommand::class,
                SupervisorStartupCommand::class,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function registerPublishers()
    {
        $this->publishes(
        [
            __DIR__."/../../config/supervisor.php" => config_path("supervisor.php"),
            __DIR__."/../../config/server.php" => config_path("octane.php"),
            __DIR__."/../../config/websockets.php" => config_path("websockets.php"),
        ],

        "tripteki-laravelphp-supervisor");

        if (! static::shouldRunMigrations()) {

            $this->publishes(
            [
                __DIR__."/../../../../beyondcode/laravel-websockets/database/migrations" => database_path("migrations"),
            ],

            "tripteki-laravelphp-supervisor-migrations");
        }
    }
};
