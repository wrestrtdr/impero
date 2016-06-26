<?php namespace Pckg\Furs\Provider;

use Pckg\Framework\Provider;
use Pckg\Furs\Console\FursConfirmation;

class Config extends Provider
{

    public function consoles() {
        return [
            FursConfirmation::class,
        ];
    }

}