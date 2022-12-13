<?php

namespace Tripteki\Supervisor\Supervisor;

use Tripteki\Supervisor\Concerns\LiveTrait;
use Symfony\Component\Process\PhpExecutableFinder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class StateFile
{
    use LiveTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param array $configuration
     * @return void
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function supervisorPath()
    {
        return storage_path("logs/supervisor");
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $isDeleted = false;

        Arr::map(Arr::pluck($this->read()["apps"], "script"), function ($script) {

            __exec__("pkill -f \"{$script}\"");
        });

        if (File::exists($this->getPath())) {

            File::deleteDirectory($this->supervisorPath(), true);
            File::delete($this->getPath());

            $isDeleted = true;
        }

        return $isDeleted;
    }

    /**
     * @return int
     */
    public function write()
    {
        return File::put($this->getPath(), json_encode($this->read(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * @return array
     */
    public function read()
    {
        $supervisor = [];
        $configurations = $this->getConfiguration();

        foreach ($configurations as $processname => $configuration) {

            $isLive = $this->isLive();

            $interpreter = $configuration["interpreter"] ?? ($executeable = (new PhpExecutableFinder)->find());
            $commandDevelopment = $configuration["command:development"] ?? null;
            $commandProduction = $configuration["command:production"] ?? null;
            $command = $configuration["command"] ?? ($isLive ? $commandProduction : $commandDevelopment);
            $process = $configuration["process"] ?? 1;
            $delay = $configuration["delay"] ?? 0;
            $stdout = $configuration["stdout"] ?? "/dev/stdout";
            $stderr = $configuration["stderr"] ?? "/dev/stderr";
            $increment = $configuration["increment"] ?? [];

            $context = [

                "name" => $processname,
                "instances" => $process != "auto" ? $process : "max",
                "exec_mode" => "fork", // "cluster" //
                "script" => $command,
                "out_file" => $stdout,
                "error_file" => $stderr,
                "restart_delay" => $delay,
                "cwd" => base_path(),
                "env" => [ "NODE_ENV" => $isLive ? "production" : "development", ],
            ];

            $artisan = defined("ARTISAN_BINARY") ? ARTISAN_BINARY : "artisan";

            if (Arr::isAssoc($increment)) {

                $key = array_key_first($increment);
                $value = $increment[$key];

                $context["env"][$key] = $value;
                $context["increment_var"] = $key;
            }

            if ($interpreter == $executeable && Arr::first($context["script"]) != $artisan) {

                $context["script"] = Arr::prepend($context["script"], $artisan);
            }

            $context["script"] = $interpreter." ".implode(" ", $context["script"]);

            $supervisor[] = $context;
        }

        return [ "apps" => $supervisor, ];
    }

    /**
     * @return array
     */
    public function list()
    {
        $processes = [];

        if (File::exists($pm2pid = $this->supervisorPath()."/pm2.pid") && File::exists($pids = $this->supervisorPath()."/pids")) {

            $processes[] = [

                "name" => "manager",
                "id" => File::get($pm2pid),
            ];

            Arr::map(File::files($pids), function ($file) use (&$processes, $pids) {

                $processes[] = [

                    "name" => Str::of($file->getFileName())->replaceMatches("/-[0-9]+\.pid$/", ""),
                    "id" => File::get($pids."/".$file->getFileName()),
                ];
            });
        }

        return $processes;
    }
};
