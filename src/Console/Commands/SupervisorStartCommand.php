<?php

namespace Tripteki\Supervisor\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessSignaledException;

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

        try {

            $this->trap([ SIGINT, SIGTERM, SIGQUIT, ], function () use (&$supervisor) {

                if ($supervisor) {

                    $this->shouldKeepRunning = false;
                }

                $this->supervisor->delete();
            });

            if ($this->supervisor->write()) {

                $supervisor = $this->exec("start", "foreground");
            }

        } catch (ProcessSignaledException $thrower) {}
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
