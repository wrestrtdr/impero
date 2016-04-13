<?php namespace Impero\Ftp\Form;

use Pckg\Htmlbuilder\Element\Form;

class Ftp extends Form
{

    public function initFields()
    {
        $this->addHidden('id');

        $this->addText('username')
            ->setLabel('Username:')
            ->required()
            ->unique();

        $this->addPassword('password')
            ->setLabel('Password:')
            ->required();

        $this->addText('path')
            ->setLabel('Path:');

        $this->addSubmit();

        return $this;
    }

}