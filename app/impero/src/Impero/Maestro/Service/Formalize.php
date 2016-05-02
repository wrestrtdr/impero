<?php namespace Impero\Maestro\Service;

use Pckg\Database\Record;
use Pckg\Htmlbuilder\Element\Form;

class Formalize
{

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Record
     */
    protected $record;

    /**
     * @var string
     */
    protected $title;

    /**
     * Formalize constructor.
     * @param Form   $form
     * @param Record $record
     */
    public function __construct(Form $form, Record $record)
    {
        $this->form = $form;
        $this->record = $record;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function __toString()
    {
        return (string)view('Impero/Maestro:formalize', [
            'record'    => $this->record,
            'form'      => $this->form,
            'formalize' => $this,
        ]);
    }

}