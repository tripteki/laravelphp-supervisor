<?php

namespace Tripteki\Supervisor\Console\Commands;

class SupervisorStatusCommand extends Supervisor
{
    /**
     * @var string
     */
    protected $signature = "supervisor:status";

    /**
     * @var string
     */
    protected $description = "Status the supervisor";

    /**
     * @return int
     */
    public function handle()
    {
        $this->exec("status", "background");
    }
};
