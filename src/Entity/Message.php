<?php

namespace FOS\ChatBundle\Entity;

use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\Message as BaseMessage;
use FOS\ChatBundle\Model\MessageMetadata as ModelMessageMetadata;

abstract class Message extends BaseMessage
{
    /**
     * @return Collection<int, MessageMetadata>
     */
    public function getAllMetadata() : Collection
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function addMetadata(ModelMessageMetadata $meta): void
    {
        $meta->setMessage($this);
        parent::addMetadata($meta);
    }
}
