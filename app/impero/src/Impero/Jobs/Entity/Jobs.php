<?php namespace Impero\Jobs\Entity;

use Impero\Jobs\Record\Job;
use Pckg\Database\Entity;

class Jobs extends Entity
{

    protected $record = Job::class;

}