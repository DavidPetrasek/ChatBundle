<?php

namespace FOS\ChatBundle\Document;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use PHPUnit\Framework\TestCase;

class ThreadDenormalizerTest extends TestCase
{
    private $dates;

    /**
     * This method should be setUp(): void
     * For compatibility reasons with old versions of PHP, we cannot use neither setUp(): void nor setUp().
     */
    private function setUpBeforeTest()
    {
        $this->markTestIncomplete('Broken, needs to be fixed');

        $this->dates = [
            new DateTimeImmutable('- 3 days'),
            new DateTimeImmutable('- 2 days'),
            new DateTimeImmutable('- 1 days'),
            new DateTimeImmutable('- 1 hour'),
        ];
    }

    public function testDenormalize(): void
    {
        $this->setUpBeforeTest();

        $thread = new TestThread();
        $user1 = $this->createParticipantMock('u1');
        $user2 = $this->createParticipantMock('u2');

        /*
         * First message
         */
        $message = $this->createMessageMock($user1, $user2, $this->dates[0]);
        $thread->setSubject('Test thread subject');
        $thread->addParticipant($user2);
        $thread->addMessage($message);

        $this->assertSame([$user1, $user2], $thread->getParticipants());
        $this->assertSame(['u2' => $this->dates[0]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByOtherParticipant());
        $this->assertSame(['u1' => $this->dates[0]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByParticipant());

        /*
         * Second message
         */
        $message = $this->createMessageMock($user2, $user1, $this->dates[1]);
        $thread->addMessage($message);

        $this->assertSame([$user1, $user2], $thread->getParticipants());
        $this->assertSame(['u1' => $this->dates[1]->getTimestamp(), 'u2' => $this->dates[0]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByOtherParticipant());
        $this->assertSame(['u1' => $this->dates[0]->getTimestamp(), 'u2' => $this->dates[1]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByParticipant());

        /*
         * Third message
         */
        $message = $this->createMessageMock($user2, $user1, $this->dates[2]);
        $thread->addMessage($message);

        $this->assertSame([$user1, $user2], $thread->getParticipants());
        $this->assertSame(['u1' => $this->dates[2]->getTimestamp(), 'u2' => $this->dates[0]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByOtherParticipant());
        $this->assertSame(['u1' => $this->dates[0]->getTimestamp(), 'u2' => $this->dates[2]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByParticipant());

        /*
         * Fourth message
         */
        $message = $this->createMessageMock($user1, $user2, $this->dates[3]);
        $thread->addMessage($message);

        $this->assertSame([$user1, $user2], $thread->getParticipants());
        $this->assertSame(['u1' => $this->dates[2]->getTimestamp(), 'u2' => $this->dates[3]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByOtherParticipant());
        $this->assertSame(['u1' => $this->dates[3]->getTimestamp(), 'u2' => $this->dates[2]->getTimestamp()], $thread->getDatesOfLastMessageWrittenByParticipant());

        $this->assertEquals('test thread subject hi dude', $thread->getKeywords());
        $this->assertSame(['u1' => false, 'u2' => false], $thread->getIsDeletedByParticipant());
    }

    private function createMessageMock($sender, $recipient, DateTimeImmutable $date)
    {
        $message = $this->getMockBuilder(\FOS\ChatBundle\Document\Message::class)
            ->getMock();

        $message->expects($this->atLeastOnce())
            ->method('getSender')
            ->will($this->returnValue($sender));
        $message->expects($this->atLeastOnce())
            ->method('getTimestamp')
            ->will($this->returnValue($date->getTimestamp()));
        $message->expects($this->atLeastOnce())
            ->method('ensureIsReadByParticipant');
        $message->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('hi dude'));

        return $message;
    }

    private function createParticipantMock($id)
    {
        $user = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();

        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        return $user;
    }
}

class TestThread extends Thread
{
    public $datesOfLastMessageWrittenByParticipant;

    public $datesOfLastMessageWrittenByOtherParticipant;

    public $isDeletedByParticipant;

    public function getDatesOfLastMessageWrittenByParticipant()
    {
        return $this->datesOfLastMessageWrittenByParticipant;
    }

    public function getDatesOfLastMessageWrittenByOtherParticipant()
    {
        return $this->datesOfLastMessageWrittenByOtherParticipant;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getIsDeletedByParticipant()
    {
        return $this->isDeletedByParticipant;
    }

    #[\Override]
    public function addMessage(MessageInterface $message): void
    {
        parent::addMessage($message);

        $this->sortDenormalizedProperties();
    }

    /**
     * Sort denormalized properties to ease testing.
     */
    private function sortDenormalizedProperties()
    {
        ksort($this->isDeletedByParticipant);
        ksort($this->datesOfLastMessageWrittenByParticipant);
        ksort($this->datesOfLastMessageWrittenByOtherParticipant);
        $participants = $this->participants->toArray();
        usort($participants, fn(ParticipantInterface $p1, ParticipantInterface $p2): bool => $p1->getId() > $p2->getId());
        $this->participants = new ArrayCollection($participants);
    }
}
