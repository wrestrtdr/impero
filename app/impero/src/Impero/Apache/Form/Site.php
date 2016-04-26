<?php namespace Impero\Apache\Form;

use Pckg\Htmlbuilder\Decorator\Method\Bootstrap;
use Pckg\Htmlbuilder\Element\Form;
use Pckg\Htmlbuilder\Element\Form\ResolvesOnRequest;

class Site extends Form\Bootstrap implements ResolvesOnRequest
{

    public function initFields()
    {
        $this->addHidden('id');

        $this->addText('server_name')
            ->setLabel('Server name (URL)')/*
            ->required()
            ->unique()*/;

        $this->addText('server_alias')
            ->setLabel('Server aliases:');

        $this->addText('document_root')
            ->setLabel('Document root:');

        $this->addSelect('ssl')
            ->setLabel('SSL')
            ->setPlaceholder('Select SSL method')
            ->addOptions([
                ''            => 'Disabled (default)',
                'file'        => 'File',
                'letsencrypt' => 'LetsEncrypt',
            ]);
        $this->addSslFileFields();
        $this->addSslLetsencrypt();
        $this->addLogFields();

        $this->addCheckbox('enabled');

        $this->addSubmit();

        return $this;
    }

    private function addSslFileFields()
    {
        $this->addFile('ssl_certificate_key')
            ->setLabel('*.crt')/*
            ->extensions(['crt'])
            ->requiredWhen('ssl', 'file')
            ->enabledWhen('ssl', 'file')*/;

        $this->addFile('ssl_certificate_key_file')
            ->setLabel('*.key')/*
            ->extensions(['key'])
            ->requiredWhen('ssl', 'file')
            ->enabledWhen('ssl', 'file')*/;
    }

    private function addSslLetsencrypt()
    {
        $this->addCheckbox('ssl_letsencrypt_autorenew')
            ->setLabel('Automatically renew certificate');
    }

    private function addLogFields()
    {
        $this->addCheckbox('error_log')
            ->setLabel('Error log');
        
        $this->addCheckbox('access_log')
            ->setLabel('Access log');
    }

}