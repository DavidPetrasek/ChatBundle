<?php

namespace FOS\ChatBundle\Service\EntityManager;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\ModelManager\MessageManager as BaseMessageManager;

/**
 * Default ORM MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
{
    private readonly \Doctrine\ORM\EntityRepository $repository;

    private readonly string $class;

    private readonly string $metaClass;

    public function __construct(private readonly EntityManager $em, string $class, string $metaClass)
    {
        $this->repository = $this->em->getRepository($class);
        $this->class = $this->em->getClassMetadata($class)->name;
        $this->metaClass = $this->em->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageByThreadQueryBuilder(int|ThreadInterface $thread): QueryBuilder
    {
        return $this->repository->createQueryBuilder('m')
            ->where('m.thread = ?1')
            ->setParameter(1, $thread);
    }

    /**
     * {@inheritdoc}
     */
    public function getNbSentMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread): QueryBuilder
    {
        return $this->repository->createQueryBuilder('m')
            ->select('count(m.id)')

            ->where('m.sender = ?1 AND m.thread = ?2')
            ->setParameter(1, $participant)
            ->setParameter(2, $thread);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getNbSentMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread): int
    {
        return $this->getNbSentMessageByParticipantAndThreadQueryBuilder($participant, $thread)->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant): QueryBuilder
    {
        return $this->repository->createQueryBuilder('m')
            ->innerJoin('m.metadata', 'mm')
            ->innerJoin('mm.participant', 'p')

            ->where('p.id = :participant_id')
            ->setParameter('participant_id', $participant)

            ->andWhere('m.sender != :sender')
            ->setParameter('sender', $participant)

            ->andWhere('mm.read = :read')
            ->setParameter('read', false, Types::BOOLEAN);
    }

    /**
     * {@inheritdoc}
     */
    public function getUnreadMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread): QueryBuilder
    {
        return $this->getUnreadMessageByParticipantQueryBuilder($participant)
            ->andWhere('m.thread = :thread')
            ->setParameter('thread', $thread);
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread): QueryBuilder
    {
        $builder = $this->getUnreadMessageByParticipantAndThreadQueryBuilder($participant, $thread);

        return $builder
            ->select($builder->expr()->count('m.id'));
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant): QueryBuilder
    {
        $builder = $this->getUnreadMessageByParticipantQueryBuilder($participant);

        return $builder
            ->select($builder->expr()->count('m.id'));
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant): int
    {
        return (int) $this->getNbUnreadMessageByParticipantQueryBuilder($participant)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread): int
    {
        return (int) $this->getNbUnreadMessageByParticipantAndThreadQueryBuilder($participant, $thread)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstMessageByThreadQueryBuilder(ThreadInterface $thread): QueryBuilder
    {
        return $this->repository->createQueryBuilder('m')            
            ->where('m.thread = ?1')
            ->setParameter(1, $thread)

            ->orderBy('m.id', 'ASC')
            ->setMaxResults(1);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstMessageByThread(ThreadInterface $thread): ?MessageInterface
    {
        return $this->getFirstMessageByThreadQueryBuilder($thread)->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMessageByThreadQueryBuilder(ThreadInterface $thread): QueryBuilder
    {
        return $this->repository->createQueryBuilder('m')            
            ->where('m.thread = ?1')
            ->setParameter(1, $thread)

            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMessageByThread(ThreadInterface $thread): ?MessageInterface
    {
        return $this->getLastMessageByThreadQueryBuilder($thread)->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant): void
    {
        $readable->setReadByParticipant($participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant): void
    {
        $readable->setReadByParticipant($participant, false);
    }

    /**
     * Marks all messages of this thread as read by this participant.
     */
    public function markReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, bool $read): void
    {
        foreach ($thread->getMessages() as $message) {
            $this->markReadByParticipant($message, $participant, $read);
        }
    }

    /**
     * Marks the message as read or unread by this participant.
     */
    private function markReadByParticipant(MessageInterface $message, ParticipantInterface $participant, bool $read): void
    {
        $meta = $message->getMetadataForParticipant($participant);
        if (!$meta || $meta->isRead() === $read) {
            return;
        }

        $this->em->createQueryBuilder()
            ->update($this->metaClass, 'm')
            ->set('m.read', '?1')
            ->setParameter(1, $read, Types::BOOLEAN)

            ->set('m.readAt', '?2')
            ->setParameter(2, $read ? new \DateTimeImmutable() : null, Types::DATETIME_IMMUTABLE)

            ->where('m.id = :id')
            ->setParameter('id', $meta)

            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(MessageInterface $message, $andFlush = true): void
    {
        $this->denormalize($message);
        $this->em->persist($message);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks.
     */
    private function denormalize(MessageInterface $message): void
    {
        $this->doMetadata($message);
    }

    /**
     * Ensures that the message metadata are up to date.
     */
    private function doMetadata(MessageInterface $message): void
    {
        foreach ($message->getThread()->getAllMetadata() as $threadMeta) {
            $meta = $message->getMetadataForParticipant($threadMeta->getParticipant());
            if (!$meta) 
            {
                $threadParticipant = $threadMeta->getParticipant();
                $meta = $this->createMessageMetadata();     
                $meta->setParticipant($threadParticipant);
                
                if ($message->getSender() === $threadParticipant) {$meta->setRead(true);}

                $message->addMetadata($meta);
            }
        }
    }

    private function createMessageMetadata()
    {
        return new $this->metaClass();
    }
}
