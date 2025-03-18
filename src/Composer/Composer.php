<?php

namespace FOS\ChatBundle\Composer;

use FOS\ChatBundle\MessageBuilder\NewThreadMessageBuilder;
use FOS\ChatBundle\MessageBuilder\ReplyMessageBuilder;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\ModelManager\MessageManagerInterface;
use FOS\ChatBundle\ModelManager\ThreadManagerInterface;

/**
 * Factory for message builders.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Composer implements ComposerInterface
{
    public function __construct(
        /**
         * Message manager.
         */
        private MessageManagerInterface $messageManager,
        /**
         * Thread manager.
         */
        private ThreadManagerInterface $threadManager
    )
    {
    }

    /**
     * Starts composing a message, starting a new thread.
     */
    public function newThread(): NewThreadMessageBuilder
    {
        $thread = $this->threadManager->createThread();
        $message = $this->messageManager->createMessage();

        return new NewThreadMessageBuilder($message, $thread);
    }

    /**
     * Starts composing a message in a reply to a thread.
     */
    public function reply(ThreadInterface $thread): ReplyMessageBuilder
    {
        $message = $this->messageManager->createMessage();

        return new ReplyMessageBuilder($message, $thread);
    }
}
