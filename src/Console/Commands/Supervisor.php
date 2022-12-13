<?php

namespace Tripteki\Supervisor\Console\Commands;

use Tripteki\Supervisor\Supervisor\StateFile;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Supervisor extends Command
{
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
        $this->supervisor->setPath(config("supervisor.state_file") ?? storage_path("logs/supervisor.json"));
    }

    /**
     * @param string $command
     * @param string $mode
     * @return \Symfony\Component\Process\Process|void
     */
    public function exec($command, $mode)
    {
        $requirements = json_decode(__exec__("npm list pm2 chokidar --depth=0 --json"), true);

        if (! Arr::exists($requirements, "dependencies") ||
            ! Arr::exists($requirements["dependencies"], "pm2") ||
            ! Arr::exists($requirements["dependencies"], "chokidar")) {

                $this->error("You need to install (pm2) and (chokidar) locally first and not as globally!");

                return;
        }

        $supervisor = "npx"." ".$this->{$mode}()." ".$command." ".$this->supervisor->getPath();
        $path = [ "PM2_HOME" => $this->supervisor->supervisorPath(), ];

        if ($mode == "foreground") {

            $supervisor = __spawn__($supervisor, $path, function ($isError, $data) {

                if ($isError) {

                    file_put_contents("php://stderr", $data, FILE_APPEND);

                } else {

                    file_put_contents("php://stdout", $data, FILE_APPEND);
                }
            });

        } else if ($mode == "background") {

            $supervisor = __spawn__($supervisor, $path);

            if ($command == "start") {

                $this->info("Supervisor started!");

            } else if ($command == "reload") {

                $this->info("Supervisor reloaded!");

            } else if ($command == "stop") {

                $this->info("Supervisor stopped!");

            } else if ($command == "status") {

                $this->newLine();
                $this->table([ "Process Name", "Process Id", ], $this->supervisor->list());
                $this->newLine();
            }
        }

        return $supervisor;
    }

    /**
     * @return string
     */
    protected function foreground()
    {
        return "pm2-runtime";
    }

    /**
     * @return string
     */
    protected function background()
    {
        return "pm2";
    }
};
