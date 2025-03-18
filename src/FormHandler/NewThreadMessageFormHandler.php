<?php

namespace FOS\ChatBundle\FormHandler;

use FOS\ChatBundle\FormModel\AbstractMessage;
use FOS\ChatBundle\FormModel\NewThreadMessage;
use FOS\ChatBundle\Model\MessageInterface;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data.
     */
    private function composeMessage(NewThreadMessage $message) : MessageInterface
    {
        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipient($message->getRecipient())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
