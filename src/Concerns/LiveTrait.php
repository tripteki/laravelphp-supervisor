<?php

namespace Tripteki\Supervisor\Concerns;

trait LiveTrait
{
    /**
     * @return bool
     */
    protected function isLive()
    {
        return app()->environment("production");
    }
};
