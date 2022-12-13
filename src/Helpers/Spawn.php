<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

if (! function_exists("__spawn__"))
{
    /**
     * @param string $command
     * @param array $environments
     * @param \Closure|callable|null $fnstdout
     * @param \Symfony\Component\Process\InputStream|\resource|null $stdin
     * @return mixed
     */
    function __spawn__($command, $environments = [], \Closure|null $fnstdout = null, InputStream|\resource|null $stdin = null)
    {
        $commands = Str::of($command)->explode(" ")->toArray();

        $executor = (new ExecutableFinder)->find(Arr::first($commands)) ?: throw new \Exception("The executor cannot be found.");
        $executeble = Arr::prepend(Arr::except($commands, 0), $executor);

        $subprocess = new Process($executeble, null, $environments);
        $subprocess->setTimeout(null);
        $subprocess->setIdleTimeout(null);

        if ($stdin) {

            $subprocess->setInput($stdin);
        }

        // $subprocess->run(); // // "Sync" //
        $subprocess->start(); // "ASync" //

        if ($fnstdout instanceof \Closure) {

            $subprocess->wait(function ($type, $data) use ($fnstdout) {

                $isError = false;

                if ($type === Process::ERR) {

                    $isError = true;
                }

                $fnstdout($isError, $data);
            });

        } else {

            $subprocess->wait();
        }

        return $subprocess;
    };
}
