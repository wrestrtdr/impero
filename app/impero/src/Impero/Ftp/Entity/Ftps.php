<?php namespace Impero\Ftp\Entity;

use Impero\Ftp\Record\Ftp;
use Pckg\Auth\Entity\Users;
use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;
use Pckg\Database\Entity;

class Ftps extends Entity implements MaestroEntity
{

    protected $record = Ftp::class;

    /**
     * Build edit url.
     *
     * @return string
     */
    public function getAddUrl()
    {
        return url('ftp.add');
    }

    public function user()
    {
        return $this->belongsTo(Users::class)
            ->foreignKey('user_id')
            ->fill('user');
    }

}