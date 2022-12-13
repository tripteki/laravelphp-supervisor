<?php

use Laravel\Octane\Facades\Octane as Server;

if (! function_exists("__async__"))
{
    /**
     * @param \Closure|callable $fn
     * @return mixed
     */
    function __async__(\Closure $fn)
    {
        return Server::concurrently(
        [
            $fn,
        ]);
    };
}
