<?php

namespace FOS\ChatBundle\Tests\EntityManager;

use Doctrine\ORM\EntityManager;
use FOS\ChatBundle\Service\EntityManager\ThreadManager;
use FOS\ChatBundle\Service\EntityManager\MessageManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ThreadManagerTest.
 *
 * @author Tobias Nyholm
 */
class ThreadManagerTest extends TestCase
{
    private $user;
    private $date;
    private $em;
    private $messageManager;
    private $threadManager;

    public function setUp(): void
    {
        $this->user = $this->createParticipantMock(4711);
        $this->date = new \DateTimeImmutable('2013-12-25');
        
        // Create mocks for EntityManager and MessageManager
        $this->em = $this->createMock(EntityManager::class);
        $this->messageManager = $this->createMock(MessageManager::class);
        
        // Provide a repository and class metadata objects so the constructor can access them
        $threadClass = \FOS\ChatBundle\Entity\Thread::class;
        $metaClass = \FOS\ChatBundle\Entity\ThreadMetadata::class;

        $repository = $this->createMock(\Doctrine\ORM\EntityRepository::class);

        $classMeta = $this->getMockBuilder(\Doctrine\ORM\Mapping\ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $classMeta->name = $threadClass;

        $metaMeta = $this->getMockBuilder(\Doctrine\ORM\Mapping\ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metaMeta->name = $metaClass;

        $this->em->method('getRepository')->willReturnMap([
            [$threadClass, $repository],
        ]);

        $this->em->method('getClassMetadata')->willReturnMap([
            [$threadClass, $classMeta],
            [$metaClass, $metaMeta],
        ]);

        // Create ThreadManager with mocked dependencies
        $this->threadManager = new ThreadManager(
            $this->em,
            $threadClass,
            $metaClass,
            $this->messageManager
        );
    }

    /**
     * Usual test case where neither createdBy or createdAt is set.
     */
    public function testSaveThreadSetsCreatedByAndAt(): void
    {
        $thread = $this->createThreadMock();
        $thread->expects($this->once())->method('getFirstMessage')
            ->willReturn($this->createMessageMock());
        $thread->expects($this->once())->method('setCreatedBy')
            ->with($this->user);
        $thread->expects($this->once())->method('setCreatedAt')
            ->with($this->date);
        $thread->expects($this->once())->method('getCreatedBy')
            ->willReturn(null);
        $thread->expects($this->once())->method('getCreatedAt')
            ->willReturn(null);

        $this->em->expects($this->once())->method('persist')->with($thread);
        $this->em->expects($this->once())->method('flush');

        $this->threadManager->saveThread($thread, true);
    }

    /**
     * Test where createdBy is already set - should not be overwritten.
     */
    public function testSaveThreadDoesNotOverwriteExistingCreatedBy(): void
    {
        $thread = $this->createThreadMock();
        $thread->expects($this->once())->method('getFirstMessage')
            ->willReturn($this->createMessageMock());
        $thread->expects($this->never())->method('setCreatedBy');
        $thread->expects($this->once())->method('setCreatedAt')
            ->with($this->date);
        $thread->expects($this->exactly(1))->method('getCreatedBy')
            ->willReturn($this->user);
        $thread->expects($this->once())->method('getCreatedAt')
            ->willReturn(null);

        $this->em->expects($this->once())->method('persist')->with($thread);
        $this->em->expects($this->once())->method('flush');

        $this->threadManager->saveThread($thread, true);
    }

    /**
     * Test where createdAt is already set - should not be overwritten.
     */
    public function testSaveThreadDoesNotOverwriteExistingCreatedAt(): void
    {
        $thread = $this->createThreadMock();
        $thread->expects($this->once())->method('getFirstMessage')
            ->willReturn($this->createMessageMock());
        $thread->expects($this->once())->method('setCreatedBy')
            ->with($this->user);
        $thread->expects($this->never())->method('setCreatedAt');
        $thread->expects($this->once())->method('getCreatedBy')
            ->willReturn(null);
        $thread->expects($this->exactly(1))->method('getCreatedAt')
            ->willReturn($this->date);

        $this->em->expects($this->once())->method('persist')->with($thread);
        $this->em->expects($this->once())->method('flush');

        $this->threadManager->saveThread($thread, true);
    }

    /**
     * Test where both createdAt and createdBy are already set - should not be modified.
     */
    public function testSaveThreadDoesNotModifyWhenBothAlreadySet(): void
    {
        $thread = $this->createThreadMock();
        $thread->expects($this->once())->method('getFirstMessage')
            ->willReturn($this->createMessageMock());
        $thread->expects($this->never())->method('setCreatedBy');
        $thread->expects($this->never())->method('setCreatedAt');
        $thread->expects($this->once())->method('getCreatedBy')
            ->willReturn($this->user);
        $thread->expects($this->once())->method('getCreatedAt')
            ->willReturn($this->date);

        $this->em->expects($this->once())->method('persist')->with($thread);
        $this->em->expects($this->once())->method('flush');

        $this->threadManager->saveThread($thread, true);
    }

    /**
     * Test where thread has no first message - nothing should be set.
     */
    public function testSaveThreadWithoutFirstMessageDoesNothing(): void
    {
        $thread = $this->createThreadMock();
        $thread->expects($this->once())->method('getFirstMessage')
            ->willReturn(null);
        $thread->expects($this->never())->method('setCreatedBy');
        $thread->expects($this->never())->method('setCreatedAt');

        $this->em->expects($this->once())->method('persist')->with($thread);
        $this->em->expects($this->once())->method('flush');

        $this->threadManager->saveThread($thread, true);
    }

    /**
     * Get a message mock.
     */
    private function createMessageMock() : mixed
    {
        $message = $this->getMockBuilder(\FOS\ChatBundle\Document\Message::class)
            ->getMock();

        $message->expects($this->any())
            ->method('getSender')
            ->willReturn($this->user);

        $message->expects($this->any())
            ->method('getCreatedAt')
            ->willReturn($this->date);

        return $message;
    }

    /**
     * Add expectations on the thread mock.
     */
    private function addThreadExpectations(MockObject &$thread, int $createdByCalls = 1, int $createdAtCalls = 1)
    {
        $thread->expects($this->exactly($createdByCalls))
            ->method('setCreatedBy')
            ->with($this->equalTo($this->user));

        $thread->expects($this->exactly($createdAtCalls))
            ->method('setCreatedAt')
            ->with($this->equalTo($this->date));
    }

    /**
     * Get a Participant.
     */
    private function createParticipantMock(int $id) : mixed
    {
        $participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();

        $participant->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $participant;
    }

    /**
     * Returns a thread mock.
     */
    private function createThreadMock() : mixed
    {
        return $this->getMockBuilder(\FOS\ChatBundle\Model\ThreadInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();
    }
}
