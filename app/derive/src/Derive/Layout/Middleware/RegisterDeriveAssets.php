<?php namespace Derive\Layout\Middleware;

use Derive\Layout\Provider\DeriveAssets;
use Pckg\Framework\Provider;

class RegisterDeriveAssets extends Provider
{

    protected $provider;

    public function __construct(DeriveAssets $provider)
    {
        $this->provider = $provider;
    }

    public function execute(callable $next)
    {
        $this->provider->register();

        return $next;
    }

}