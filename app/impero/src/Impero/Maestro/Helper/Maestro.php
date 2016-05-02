<?php namespace Impero\Maestro\Helper;

use Impero\Maestro\Service\Formalize;
use Impero\Maestro\Service\Tabelize;
use Pckg\Database\Entity;
use Pckg\Database\Record;
use Pckg\Htmlbuilder\Element\Form;

trait Maestro
{

    protected function tabelize(Entity $entity, $fields = [], $title)
    {
        return (new Tabelize($entity, $fields))->setTitle($title);
    }

    protected function formalize(Form $form, Record $record, $title)
    {
        return (new Formalize($form, $record))->setTitle($title);
    }

}