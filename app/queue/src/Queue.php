<?php

use Pckg\Framework\Provider;
use Pckg\Queue\Provider\Queue as QueueProvider;

class Queue extends Provider
{

    public function providers()
    {
        return [
            QueueProvider::class,
        ];
    }

}