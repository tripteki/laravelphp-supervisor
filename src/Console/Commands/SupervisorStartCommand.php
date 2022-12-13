<?php

namespace Tripteki\Supervisor\Console\Commands;

use Symfony\Component\Process\Process;

class SupervisorStartCommand extends Supervisor
{
    /**
     * @var string
     */
    protected $signature = "supervisor:start {mode=foreground}";

    /**
     * @var string
     */
    protected $description = "Start the supervisor";

    /**
     * @return int
     */
    public function handle()
    {
        $mode = $this->argument("mode");

        switch ($mode) {

            case "foreground":
            $this->createForegroundProcess();
            break;

            case "background":
            $this->createBackgroundProcess();
            break;

            default:
            $this->createForegroundProcess();
        }
    }

    /**
     * @return void
     */
    public function createForegroundProcess()
    {
        $supervisor = null;

        $this->trap([ SIGINT, SIGTERM, SIGQUIT, ], function () use (&$supervisor) {

            if ($supervisor) {

                $supervisor->stop(3, SIGINT);
                $supervisor->stop(15, SIGTERM);
            }

            $this->supervisor->delete();
        });

        if ($this->supervisor->write()) {

            $supervisor = $this->exec("start", "foreground");
        }
    }

    /**
     * @return void
     */
    public function createBackgroundProcess()
    {
        if ($this->supervisor->write()) {

            $this->exec("start", "background");
        }
    }
};
