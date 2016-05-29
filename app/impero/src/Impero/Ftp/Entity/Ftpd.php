<?php namespace Impero\Ftp\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Ftpd extends Entity
{

    protected $repositoryName = Repository::class . '.pureftpd';

    protected $primaryKey = 'comment';

}