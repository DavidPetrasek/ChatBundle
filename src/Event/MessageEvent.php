<?php

namespace FOS\ChatBundle\Event;

use FOS\ChatBundle\Model\MessageInterface;

class MessageEvent extends ThreadEvent
{
    private readonly \FOS\ChatBundle\Model\MessageInterface $message;

    public function __construct(MessageInterface $message)
    {
        parent::__construct($message->getThread());

        $this->message = $message;
    }

    public function getMessage() : MessageInterface
    {
        return $this->message;
    }
}
