<?php namespace Impero\Ftp\Controller;

use Impero\Ftp\Entity\Ftps;
use Impero\Ftp\Form\Ftp as FtpForm;
use Impero\Ftp\Record\Ftp as FtpRecord;
use Pckg\Database\Helper\Traits;

class Ftp
{

    use Traits;

    public function getIndexAction(Ftps $ftps)
    {
        return view('index', [
            'ftps' => $ftps->all(),
        ]);
    }

    public function getAddAction(FtpForm $ftpForm, FtpRecord $ftpRecord)
    {
        $ftpForm->useRecordDatasource()
            ->setRecord($ftpRecord);

        return view('add', [
            'ftpForm' => $ftpForm,
        ]);
    }

    public function postAddAction(FtpForm $ftpForm, FtpRecord $ftpRecord)
    {
        $ftpForm->useRecordDatasource()
            ->setRecord($ftpRecord);

        $ftpRecord->user_id = $this->auth()->getUser()->id;

        return $this->response()->redirect();
    }

    public function getEditAction(FtpForm $ftpForm, FtpRecord $ftpRecord)
    {
        $ftpForm->useRecordDatasource()
            ->setRecord($ftpRecord);

        return view('edit', [
            'ftpForm' => $ftpForm,
        ]);
    }

    public function postEditAction(FtpForm $ftpForm, FtpRecord $ftpRecord)
    {
        $ftpForm->useRecordDatasource()
            ->setRecord($ftpRecord);

        return $this->response()->redirect();
    }

}
