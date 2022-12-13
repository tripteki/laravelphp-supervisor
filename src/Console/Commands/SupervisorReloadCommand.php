<?php

namespace Tripteki\Supervisor\Console\Commands;

class SupervisorReloadCommand extends Supervisor
{
    /**
     * @var string
     */
    protected $signature = "supervisor:reload";

    /**
     * @var string
     */
    protected $description = "Reload the supervisor";

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->supervisor->write()) {

            $this->exec("reload", "background");
        }
    }
};
