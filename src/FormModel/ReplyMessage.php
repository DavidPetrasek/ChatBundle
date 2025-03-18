<?php

namespace FOS\ChatBundle\FormModel;

use FOS\ChatBundle\Model\ThreadInterface;

class ReplyMessage extends AbstractMessage
{
    /**
     * The thread we reply to.
     */
    private ThreadInterface $thread;

    public function getThread() : ThreadInterface
    {
        return $this->thread;
    }

    public function setThread(ThreadInterface $thread): void
    {
        $this->thread = $thread;
    }
}
