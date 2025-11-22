<?php

namespace FOS\ChatBundle\Document;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use PHPUnit\Framework\TestCase;

class ThreadDenormalizerTest extends TestCase
{
    private $dates;

    public function setUp(): void
    {
        $this->markTestIncomplete();
        
        $this->dates = [
            new DateTimeImmutable('- 3 days'),
            new DateTimeImmutable('- 2 days'),
            new DateTimeImmutable('- 1 days'),
            new DateTimeImmutable('- 1 hour'),
        ];
    }

    public function testDenormalize(): void
    {
        $thread = new TestThread();
        $user1 = $this->createParticipantMock(1);
        $user2 = $this->createParticipantMock(2);

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
        $this->assertSame(['u1' => false, 'u2' => false], $thread->isDeletedByParticipant($user1));
    }

    private function createMessageMock($sender, $recipient, DateTimeImmutable $date)
    {
        $message = $this->getMockBuilder(\FOS\ChatBundle\Document\Message::class)
            ->getMock();

        $message->expects($this->atLeastOnce())
            ->method('getSender')
            ->willReturn($sender);
        $message->expects($this->atLeastOnce())
            ->method('getTimestamp')
            ->willReturn($date->getTimestamp());
        $message->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn('hi dude');

        return $message;
    }

    private function createParticipantMock(int $id)
    {
        $user = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();

        $user->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $user;
    }
}

class TestThread extends Thread
{
    public array $datesOfLastMessageWrittenByParticipant = [];

    public array $datesOfLastMessageWrittenByOtherParticipant = [];

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

    public function isDeletedByParticipant(ParticipantInterface $participant): bool
    {
        return $this->isDeletedByParticipant;
    }

    public function getAllMetadata(): Collection
    {
        throw new \Exception('Not implemented');
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
        ksort($this->datesOfLastMessageWrittenByParticipant);
        ksort($this->datesOfLastMessageWrittenByOtherParticipant);
        $participants = $this->participants->toArray();
        usort($participants, fn(ParticipantInterface $p1, ParticipantInterface $p2): bool => $p1->getId() > $p2->getId());
        $this->participants = new ArrayCollection($participants);
    }
}
