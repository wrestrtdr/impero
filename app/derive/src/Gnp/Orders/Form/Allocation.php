<?php namespace Gnp\Orders\Form;

use Pckg\Htmlbuilder\Element\Form\Bootstrap;

class Allocation extends Bootstrap
{

    public function initFields() {
        $this->addText('appartment')
             ->setLabel(__('appartment'))
             ->setAttribute('v-model', 'appartment');

        $this->addText('checkin')
             ->setLabel(__('checkin'))
             ->setAttribute('v-model', 'checkin');

        $this->addText('people')
             ->setLabel(__('people'))
             ->setAttribute('v-model', 'people');

        $this->addButton('Save data to grouped orders')
             ->setAttribute('v-on:click.prevent', 'saveAppartment')
             ->setValue('Save attributes to grouped orders');

        $this->addButton('Update grouped orders')
             ->setAttribute('v-on:click.prevent', 'fetchAppartmentData')
             ->setValue('Update grouped orders');
    }

}