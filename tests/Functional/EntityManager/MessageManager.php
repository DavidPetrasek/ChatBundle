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

    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
    }

    public function getClass(): string
    {
        return Message::class;
    }

    public function getMessageByThreadQueryBuilder(int|ThreadInterface $thread)
    {
    }

    public function getNbSentMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread)
    {
    }

    public function getNbSentMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread) : int
    {
        return 5;
    }

    public function getNbUnreadMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread)
    {
    }

    public function getNbUnreadMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread): int
    {
        return 5;
    }

    public function getUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function getUnreadMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread)
    {
    }

    public function getNbUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function getFirstMessageByThread(ThreadInterface $thread): null|MessageInterface
    {
        return new Message();
    }

    public function getFirstMessageByThreadQueryBuilder(ThreadInterface $thread)
    {
    }

    public function getLastMessageByThread(ThreadInterface $thread): null|MessageInterface
    {
        return new Message();
    }

    public function getLastMessageByThreadQueryBuilder(ThreadInterface $thread)
    {
    }
}
