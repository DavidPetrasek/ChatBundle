<?php

namespace FOS\ChatBundle\Tests\EntityManager;

use FOS\ChatBundle\EntityManager\ThreadManager;
use FOS\ChatBundle\Model\ThreadInterface;
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

    /**
     * This method should be setUp(): void
     * For compatibility reasons with old versions of PHP, we cannot use neither setUp(): void nor setUp().
     */
    public function setUpBeforeTest(): void
    {
        $this->user = $this->createParticipantMock('4711');
        $this->date = new \DateTimeImmutable('2013-12-25');
    }

    /**
     * Usual test case where neither createdBy or createdAt is set.
     */
    public function testDoCreatedByAndAt(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->createThreadMock();
        $thread->expects($this->exactly(1))->method('getFirstMessage')
            ->will($this->returnValue($this->createMessageMock()));

        $threadManager = new TestThreadManager();
        $threadManager->doCreatedByAndAt($thread);
    }

    /**
     * Test where createdBy is set.
     */
    public function testDoCreatedByAndAtWithCreatedBy(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->createThreadMock();

        $thread->expects($this->exactly(0))->method('setCreatedBy');
        $thread->expects($this->exactly(1))->method('setCreatedAt');
        $thread->expects($this->exactly(1))->method('getCreatedBy')
            ->will($this->returnValue($this->user));

        $thread->expects($this->exactly(1))->method('getFirstMessage')
            ->will($this->returnValue($this->createMessageMock()));

        $threadManager = new TestThreadManager();
        $threadManager->doCreatedByAndAt($thread);
    }

    /**
     * Test where createdAt is set.
     */
    public function testDoCreatedByAndAtWithCreatedAt(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->createThreadMock();

        $thread->expects($this->exactly(1))->method('setCreatedBy');
        $thread->expects($this->exactly(0))->method('setCreatedAt');
        $thread->expects($this->exactly(1))->method('getCreatedAt')
            ->will($this->returnValue($this->date));

        $thread->expects($this->exactly(1))->method('getFirstMessage')
            ->will($this->returnValue($this->createMessageMock()));

        $threadManager = new TestThreadManager();
        $threadManager->doCreatedByAndAt($thread);
    }

    /**
     * Test where both craetedAt and createdBy is set.
     */
    public function testDoCreatedByAndAtWithCreatedAtAndBy(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->createThreadMock();
        $thread->expects($this->exactly(0))->method('setCreatedBy');
        $thread->expects($this->exactly(0))->method('setCreatedAt');
        $thread->expects($this->exactly(1))->method('getCreatedAt')
            ->will($this->returnValue($this->date));

        $thread->expects($this->exactly(1))->method('getCreatedBy')
            ->will($this->returnValue($this->user));

        $thread->expects($this->exactly(1))->method('getFirstMessage')
            ->will($this->returnValue($this->createMessageMock()));

        $threadManager = new TestThreadManager();
        $threadManager->doCreatedByAndAt($thread);
    }

    /**
     * Test where thread do not have a message.
     */
    public function testDoCreatedByAndNoMessage(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->createThreadMock();
        $thread->expects($this->exactly(0))->method('setCreatedBy');
        $thread->expects($this->exactly(0))->method('setCreatedAt');
        $thread->expects($this->exactly(0))
            ->method('getCreatedAt')
            ->will($this->returnValue($this->date));
        $thread->expects($this->exactly(0))
            ->method('getCreatedBy')
            ->will($this->returnValue($this->user));

        $threadManager = new TestThreadManager();
        $threadManager->doCreatedByAndAt($thread);
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
            ->will($this->returnValue($this->user));

        $message->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue($this->date));

        return $message;
    }

    /**
     * Add expectations on the thread mock.
     */
    private function addThreadExpectations(mock &$thread, int $createdByCalls = 1, int $createdAtCalls = 1)
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
    private function createParticipantMock($id) : mixed
    {
        $participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();

        $participant->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

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

class TestThreadManager extends ThreadManager
{
    /**
     * Empty constructor.
     */
    public function __construct()
    {
    }

    /**
     * Make the function public.
     */
    #[\Override]
    private function doCreatedByAndAt(ThreadInterface $thread)
    {
        return parent::doCreatedByAndAt($thread);
    }
}
