<?php

declare(strict_types=1);

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
    public function getNbUnreadMessageByParticipant(int|ParticipantInterface $participant): int
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

    public function getSentMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread)
    {
    }

    public function getNbSentMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread)
    {
    }

    public function getNbSentMessageByParticipantAndThread(int|ParticipantInterface $participant, int|ThreadInterface $thread) : int
    {
        return 5;
    }

    public function getNbUnreadMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread)
    {
    }

    public function getNbUnreadMessageByParticipantAndThread(int|ParticipantInterface $participant, int|ThreadInterface $thread): int
    {
        return 5;
    }

    public function getUnreadMessageByParticipantQueryBuilder(int|ParticipantInterface $participant)
    {
    }

    public function getUnreadMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread)
    {
    }

    public function getNbUnreadMessageByParticipantQueryBuilder(int|ParticipantInterface $participant)
    {
    }

    public function getFirstMessageByThread(int|ThreadInterface $thread): null|MessageInterface
    {
        return new Message();
    }

    public function getFirstMessageByThreadQueryBuilder(int|ThreadInterface $thread)
    {
    }

    public function getLastMessageByThread(int|ThreadInterface $thread): null|MessageInterface
    {
        return new Message();
    }

    public function getLastMessageByThreadQueryBuilder(int|ThreadInterface $thread)
    {
    }
}
