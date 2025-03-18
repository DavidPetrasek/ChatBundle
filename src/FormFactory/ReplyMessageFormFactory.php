<?php

namespace FOS\ChatBundle\FormFactory;

use FOS\ChatBundle\Model\ThreadInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ReplyMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a reply message.
     */
    public function create(ThreadInterface $thread): FormInterface
    {
        $message = $this->createModelInstance();
        $message->setThread($thread);

        return $this->formFactory->createNamed($this->formName, $this->formType, $message);
    }
}
