<?php namespace Gnp\Platform\Record;

use Gnp\Platform\Entity\Platforms;
use Pckg\Database\Record;

class Platform extends Record
{

    protected $entity = Platforms::class;

    public function getDatabaseConfig() {
        return (array)json_decode($this->database);
    }

    public function getSwitchUrl() {
        return url('derive.platform.switch', ['platform' => $this]);
    }

}