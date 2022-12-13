<?php

namespace Tripteki\Supervisor\Console\Commands;

class SupervisorStopCommand extends Supervisor
{
    /**
     * @var string
     */
    protected $signature = "supervisor:stop";

    /**
     * @var string
     */
    protected $description = "Stop the supervisor";

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->supervisor->write()) {

            $this->exec("stop", "background");

            $this->supervisor->delete();
        }
    }
};
