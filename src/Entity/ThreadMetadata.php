<?php

namespace FOS\ChatBundle\Entity;

use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Model\ThreadMetadata as BaseThreadMetadata;

abstract class ThreadMetadata extends BaseThreadMetadata
{
    protected ?int $id;

    protected ThreadInterface $thread;

    /**
     * Gets the thread map id.
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    public function getThread() : ThreadInterface
    {
        return $this->thread;
    }

    public function setThread(ThreadInterface $thread): void
    {
        $this->thread = $thread;
    }
}
