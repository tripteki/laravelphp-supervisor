<?php

use Illuminate\Support\Str;
use Laravel\Octane\Facades\Octane as Server;

if (! function_exists("__setInterval__"))
{
    /**
     * @param \Closure|callable $fn
     * @param int $delay
     * @return void
     */
    function __setInterval__(\Closure $fn, $delay)
    {
        Server::tick((string) Str::orderedUuid(), $fn)->seconds($delay / 1000);
    };
}
