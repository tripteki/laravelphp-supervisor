<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Process\ExecutableFinder;

if (! function_exists("__exec__"))
{
    /**
     * @param string $command
     * @return mixed
     */
    function __exec__($command)
    {
        $commands = Str::of($command)->explode(" ")->toArray();

        $executor = (new ExecutableFinder)->find(Arr::first($commands)) ?: throw new \Exception("The executor cannot be found.");
        $executeble = Arr::prepend(Arr::except($commands, 0), $executor);

        $process = shell_exec(implode(" ", $executeble));

        return $process;
    };
}
