<?php

namespace FOS\ChatBundle\Tests\Functional\EntityManager;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\ModelManager\MessageManager as BaseMessageManager;
use FOS\ChatBundle\Tests\Functional\Entity\Message;

/**
 * Default ORM MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
{
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant): int
    {
        return 3;
    }

    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, $read)
    {
    }

    private function markReadByParticipant(MessageInterface $message, ParticipantInterface $participant, $read)
    {
    }

    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
    }

    public function getClass(): string
    {
        return Message::class;
    }
}
