<?php namespace Pckg\Queue\Entity;

use Pckg\Database\Entity;
use Pckg\Queue\Record\Queue as QueueRecord;

class Queue extends Entity
{

    protected $record = QueueRecord::class;

    public function logs()
    {
        return $this->hasMany(QueueLogs::class)
            ->foreignKey('queue_id')
            ->primaryKey('id');
    }

    public function future()
    {
        return $this->where('execute_at', date('Y-m-d H:i:s'), '>');
    }

    public function current()
    {
        return $this->where('execute_at', date('Y-m-d H:i:s'));
    }

    public function past()
    {
        return $this->where('execute_at', date('Y-m-d H:i:s'), '<');
    }

}