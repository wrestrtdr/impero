<?php namespace Impero\Ftp\Service;

use Impero\Ftp\Entity\Ftpd;

class Ftp
{

    /**
     * @var Ftpd
     */
    protected $ftps;

    public function __construct(Ftpd $ftps)
    {
        $this->ftps = $ftps;
    }

    public function saveAccount($data)
    {
        
        $account = $this->ftps->where('comment', $data['comment'])->one();

        if (!$account) {
            $account = $this->ftps->getRecord();
        }

        if (!$data['password']) {
            unset($data['password']);
        }

        $account->set($data);
        $account->save();
    }

}