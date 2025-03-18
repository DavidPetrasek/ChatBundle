<?php

namespace FOS\ChatBundle\FormFactory;

use FOS\ChatBundle\FormModel\AbstractMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormFactory
{
    public function __construct
    (
        /**
         * The Symfony form factory.
         */
        private \Symfony\Component\Form\FormFactoryInterface $formFactory, 
        /**
         * The message form type.
         */
        private AbstractType|string $formType,
        /**
         * The name of the form.
         */
        private string $formName, 
        /**
         * The FQCN of the message model.
         */
        private string $messageClass
    )
    {}

    /**
     * Creates a new instance of the form model.
     */
    private function createModelInstance() : AbstractMessage
    {
        $class = $this->messageClass;

        return new $class();
    }
}
