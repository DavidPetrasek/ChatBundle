<?php

namespace FOS\ChatBundle\Entity;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\MessageMetadata as BaseMessageMetadata;

abstract class MessageMetadata extends BaseMessageMetadata
{
    protected ?int $id = null;

    protected MessageInterface $message;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getMessage() : MessageInterface
    {
        return $this->message;
    }

    public function setMessage(MessageInterface $message): self
    {
        $this->message = $message;

        return $this;
    }
}
