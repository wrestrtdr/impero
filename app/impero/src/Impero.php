<?php

use Impero\Impero\Provider\Config as ImperoProvider;
use Pckg\Framework\Application;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;

class Impero extends Provider
{

    public function providers()
    {
        return [
            ImperoProvider::class
        ];
    }

    public function afterwares()
    {
        return [
            EncapsulateResponse::class,
        ];
    }

}

function maestro_urls($class, $slug, $record, $resolver, $alterslug = null)
{
    if (!$alterslug) {
        $alterslug = $slug;
    }

    return array_merge_array([
        'controller' => $class,
        '',
    ], [
        '/' . $alterslug                               => [
            'name' => $slug . '.list',
            'view' => 'index',
        ],
        '/' . $alterslug . '/add'                      => [
            'name' => $slug . '.add',
            'view' => 'add',
        ],
        '/' . $alterslug . '/edit/[' . $record . ']'   => [
            'name'      => $slug . '.edit',
            'view'      => 'edit',
            'resolvers' => [
                $record => $resolver,
            ],
        ],
        '/' . $alterslug . '/delete/[' . $record . ']' => [
            'name'      => $slug . '.delete',
            'view'      => 'delete',
            'resolvers' => [
                $record => $resolver,
            ],
        ],
    ]);
}