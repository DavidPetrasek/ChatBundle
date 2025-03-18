<?php

namespace FOS\ChatBundle\FormHandler;

use FOS\ChatBundle\FormModel\AbstractMessage;
use FOS\ChatBundle\FormModel\ReplyMessage;
use FOS\ChatBundle\Model\MessageInterface;

class ReplyMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data.
     */
    private function composeMessage(ReplyMessage $message) : MessageInterface
    {
        return $this->composer->reply($message->getThread())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
