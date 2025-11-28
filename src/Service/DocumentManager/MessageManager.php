<?php

namespace FOS\ChatBundle\Service\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use FOS\ChatBundle\Document\Message;
use FOS\ChatBundle\Document\MessageMetadata;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\MessageMetadata as ModelMessageMetadata;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\ModelManager\MessageManager as BaseMessageManager;

/**
 * Default MongoDB MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
{
    private $repository;
    private readonly string $class;
    private readonly string $metaClass;

    public function __construct(private readonly DocumentManager $dm, string $class, string $metaClass)
    {
        $this->repository = $dm->getRepository($class);
        $this->class = $dm->getClassMetadata($class)->name;
        $this->metaClass = $dm->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipant(int|ParticipantInterface $participant) : int
    {
        return $this->repository->createQueryBuilder()
            ->field('unreadForParticipants')->equals($participant->getId())
            ->getQuery()
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant): void
    {
        $this->markReadByParticipant($readable, $participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant): void
    {
        $this->markReadByParticipant($readable, $participant, false);
    }

    /**
     * Marks all messages of this thread as read by this participant.
     */
    public function markReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, bool $read): void
    {
        $this->markReadByCondition($participant, $read, function (Builder $queryBuilder) use ($thread): void {
            $queryBuilder->field('thread.$id')->equals(new \MongoId($thread->getId()));
        });
    }

    /**
     * Marks the message as read or unread by this participant.
     */
    private function markReadByParticipant(MessageInterface $message, ParticipantInterface $participant, bool $read): void
    {
        $this->markReadByCondition($participant, $read, function (Builder $queryBuilder) use ($message): void {
            $queryBuilder->field('_id')->equals(new \MongoId($message->getId()));
        });
    }

    /**
     * Marks messages as read/unread
     * by updating directly the storage.
     */
    private function markReadByCondition(ParticipantInterface $participant, bool $read, \Closure $condition): void
    {
        $queryBuilder = $this->repository->createQueryBuilder();
        $condition($queryBuilder);
        $queryBuilder->update()
            ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()));

        /* If marking the message as read for a participant, we should pull
         * their ID out of the unreadForParticipants array. The same is not
         * true for the inverse. We should only add a participant ID to this
         * array if the message is not considered spam.
         */
        if ($read) {
            $queryBuilder->field('unreadForParticipants')->pull($participant->getId());
        }

        $queryBuilder
            ->field('metadata.$.read')->set($read)
            ->getQuery(['multiple' => true])
            ->execute();

        /* If marking the message as unread for a participant, add their ID to
         * the unreadForParticipants array if the message is not spam. This must
         * be done in a separate query, since the criteria is more selective.
         */
        if (!$read) {
            $queryBuilder = $this->repository->createQueryBuilder();
            $condition($queryBuilder);
            $queryBuilder->update()
                ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()))
                ->field('spam')->equals(false)
                ->field('unreadForParticipants')->addToSet($participant->getId())
                ->getQuery(['multiple' => true])
                ->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(MessageInterface $message, $andFlush = true): void
    {
        $message->denormalize();
        $this->dm->persist($message);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Creates a new MessageMetadata instance.
     */
    private function createMessageMetadata() : MessageMetadata
    {
        return new $this->metaClass();
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */
    /**
     * Performs denormalization tricks.
     */
    public function denormalize(Message $message): void
    {
        $this->doEnsureMessageMetadataExists($message);
        $message->denormalize();
    }

    /**
     * Ensures that the message has metadata for each thread participant.
     */
    private function doEnsureMessageMetadataExists(Message $message): void
    {
        if (!$thread = $message->getThread()) {
            throw new \InvalidArgumentException(sprintf('No thread is referenced in message with id "%s"', $message->getId()));
        }

        foreach ($thread->getParticipants() as $participant) {
            if (!($meta = $message->getMetadataForParticipant($participant)) instanceof ModelMessageMetadata) {
                $meta = $this->createMessageMetadata();
                $meta->setParticipant($participant);
                $message->addMetadata($meta);
            }
        }
    }
}
