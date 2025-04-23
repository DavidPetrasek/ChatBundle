<?php

namespace FOS\ChatBundle\FormModel;

use FOS\ChatBundle\Model\ParticipantInterface;

class NewThreadMessage extends AbstractMessage
{
    /**
     * The user who receives the message.
     */
    private ParticipantInterface $recipient;

    /**
     * The thread subject.
     */
    private string $subject;

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getRecipient() : ParticipantInterface
    {
        return $this->recipient;
    }

    public function setRecipient(ParticipantInterface $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }
}
