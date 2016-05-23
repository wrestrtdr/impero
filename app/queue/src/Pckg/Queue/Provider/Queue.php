<?php namespace Pckg\Queue\Provider;

use Pckg\Framework\Provider;
use Pckg\Queue\Controller\Queue as QueueController;

class Queue extends Provider
{

    public function routes()
    {
        return [
            'url' => [
                '/jobs' => [
                    'controller' => QueueController::class,
                    'view'       => 'index',
                    'name'       => 'pckg.queue.index',
                ],
            ],
        ];
    }

}