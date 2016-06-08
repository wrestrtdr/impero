<?php namespace Gnp\Orders\Form;

use Pckg\Htmlbuilder\Element\Form\Bootstrap;

class Attributes extends Bootstrap
{

    public function initFields() {
        $this->addText('appartment')
             ->setLabel(__('appartment'))
             ->setAttribute('v-model', 'appartment')
             ->setAttribute('v-on:change.prevent', 'fetchApartmentData');

        $this->addText('checkin')
             ->setLabel(__('checkin'))
             ->setAttribute('v-model', 'checkin');

        $this->addText('people')
             ->setLabel(__('people'))
             ->setAttribute('v-model', 'people');

        $this->addButton('Save appartment')
            ->setAttribute('v-on:click.prevent', 'saveAppartment')
            ->setValue('Save attributes to grouped orders');
    }

}