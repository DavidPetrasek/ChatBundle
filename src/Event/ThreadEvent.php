<?php

namespace FOS\ChatBundle\Event;

use FOS\ChatBundle\Model\ThreadInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ThreadEvent extends Event
{
    public function __construct(private readonly ThreadInterface $thread)
    {
    }

    public function getThread() : ThreadInterface
    {
        return $this->thread;
    }
}
