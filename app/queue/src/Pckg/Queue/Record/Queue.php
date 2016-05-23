<?php namespace Pckg\Queue\Record;

use Pckg\Database\Record;
use Pckg\Queue\Entity\Queue as QueueEntity;

class Queue extends Record
{

    protected $entity = QueueEntity::class;

}