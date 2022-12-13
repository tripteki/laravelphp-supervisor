<?php

namespace Tripteki\Supervisor\Console\Commands;

use Tripteki\Supervisor\Supervisor\StateFile;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;

class SupervisorStartupCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "supervisor:startup";

    /**
     * @var string
     */
    protected $description = "Generate supervisor startup configuration";

    /**
     * @var \Tripteki\Supervisor\Supervisor\StateFile
     */
    protected $supervisor;

    /**
     * @param \Tripteki\Supervisor\Supervisor\StateFile $supervisor
     * @return void
     */
    public function __construct(StateFile $supervisor)
    {
        parent::__construct();

        $this->supervisor = $supervisor;
        $this->supervisor->setConfiguration(Arr::except(config("supervisor") ?? [], "state_file"));
        $this->supervisor->setPath(base_path("ecosystem.json"));
    }

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->supervisor->write()) {

            $this->info("Supervisor startup configuration generated.");
        }
    }
};
