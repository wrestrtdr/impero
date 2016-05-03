<?php namespace Impero\Ftp\Record;

use Impero\Ftp\Entity\Ftps;
use Pckg\Maestro\Service\Contract\Record as MaestroRecord;
use Pckg\Database\Record;

class Ftp extends Record implements MaestroRecord
{

    protected $entity = Ftps::class;

    /**
     * Build edit url.
     *
     * @return string
     */
    public function getEditUrl()
    {
        return url('ftp.edit', ['ftp' => $this]);
    }

    /**
     * Build delete url.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return url('ftp.delete', ['ftp' => $this]);
    }

    public function setUserIdByAuthIfNotSet()
    {
        if (!$this->user_id) {
            $this->user_id = auth()->user('id');
        }

        return $this;
    }

}