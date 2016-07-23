<?php

use Pckg\Framework\Provider;
use Pckg\Tempus\Provider\Tempus as TempusProvider;

class Tempus extends Provider
{

    public function providers()
    {
        return [
            TempusProvider::class,
        ];
    }

}