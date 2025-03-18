<?php

namespace FOS\ChatBundle\FormHandler;

use FOS\ChatBundle\FormModel\AbstractMessage;
use FOS\ChatBundle\FormModel\NewThreadMultipleMessage;
use FOS\ChatBundle\Model\MessageInterface;

/**
 * Form handler for multiple recipients support.
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data.
     */
    private function composeMessage(NewThreadMultipleMessage $message) : MessageInterface
    {
        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipients($message->getRecipients())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
