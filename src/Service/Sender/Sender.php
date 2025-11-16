<?php

namespace FOS\ChatBundle\Service\Sender;

use FOS\ChatBundle\Event\FOSChatEvents;
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
    public function __construct(private readonly MessageManagerInterface $messageManager, private readonly ThreadManagerInterface $threadManager, private readonly EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message): void
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message, false);

        /* Note: Thread::setDeleted() depends on metadata existing for all
         * thread and message participants, so both objects must be saved first.
         * We can avoid flushing the object manager, since we must save once
         * again after undeleting the thread.
         */
        $message->getThread()->setDeleted(false);
        $this->messageManager->saveMessage($message);

        $this->dispatcher->dispatch(new MessageEvent($message), FOSChatEvents::POST_SEND);
    }
}
