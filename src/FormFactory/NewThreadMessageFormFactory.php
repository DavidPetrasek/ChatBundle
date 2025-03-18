<?php

namespace FOS\ChatBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a new thread message.
     */
    public function create(): FormInterface
    {
        return $this->formFactory->createNamed($this->formName, $this->formType, $this->createModelInstance());
    }
}
