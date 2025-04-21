<?php

namespace FOS\ChatBundle\Event;

use FOS\ChatBundle\Model\ReadableInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ReadableEvent extends Event
{
    public function __construct(private readonly ReadableInterface $readable)
    {
    }

    public function getReadable() : ReadableInterface
    {
        return $this->readable;
    }
}
