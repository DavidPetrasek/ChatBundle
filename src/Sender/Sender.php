<?php

namespace FOS\ChatBundle\Sender;

use FOS\ChatBundle\Event\FOSMessageEvents;
use FOS\ChatBundle\Event\MessageEvent;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\ModelManager\MessageManagerInterface;
use FOS\ChatBundle\ModelManager\ThreadManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Sends messages.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Sender implements SenderInterface
{
    public function __construct(private MessageManagerInterface $messageManager, private ThreadManagerInterface $threadManager, private EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message): void
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message, false);

        /* Note: Thread::setIsDeleted() depends on metadata existing for all
         * thread and message participants, so both objects must be saved first.
         * We can avoid flushing the object manager, since we must save once
         * again after undeleting the thread.
         */
        $message->getThread()->setIsDeleted(false);
        $this->messageManager->saveMessage($message);

        $this->dispatcher->dispatch(new MessageEvent($message), FOSMessageEvents::POST_SEND);
    }
}
