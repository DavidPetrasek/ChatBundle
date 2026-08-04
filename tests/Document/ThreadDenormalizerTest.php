<?php

namespace FOS\ChatBundle\Document;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\ChatBundle\Model\ParticipantInterface;
use PHPUnit\Framework\TestCase;

class ThreadDenormalizerTest extends TestCase
{
    private array $dates;

    public function setUp(): void
    {
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

        $thread->setSubject('Test thread subject');
        $thread->addParticipant($user1);
        $thread->addParticipant($user2);

        /*
         * First message (user1 -> user2)
         */
        $message1 = $this->createMessageMock($user1, $this->dates[0]);
        $thread->addMessage($message1);
        $thread->denormalize();

        $this->assertSame([$user1, $user2], $thread->getParticipants());
        $this->assertEquals($this->dates[0], $thread->getLastMessageDate());
        $this->assertEquals($this->dates[0], $thread->getMetadataForParticipant($user1)?->getLastParticipantMessageDate());
        $this->assertNull($thread->getMetadataForParticipant($user1)?->getLastMessageDate());
        $this->assertEquals($this->dates[0], $thread->getMetadataForParticipant($user2)?->getLastMessageDate());
        $this->assertNull($thread->getMetadataForParticipant($user2)?->getLastParticipantMessageDate());

        $this->assertSame([1, 2], $thread->getActiveParticipants());
        $this->assertSame([1], $thread->getActiveSenders());
        $this->assertSame([2], $thread->getActiveRecipients());

        /*
         * Second message (user2 -> user1)
         */
        $message2 = $this->createMessageMock($user2, $this->dates[1]);
        $thread->addMessage($message2);
        $thread->denormalize();

        $this->assertEquals($this->dates[1], $thread->getLastMessageDate());
        $this->assertEquals($this->dates[1], $thread->getMetadataForParticipant($user1)?->getLastMessageDate());
        $this->assertEquals($this->dates[1], $thread->getMetadataForParticipant($user2)?->getLastParticipantMessageDate());

        $this->assertSame([1, 2], $thread->getActiveSenders());
        $this->assertSame([1, 2], $thread->getActiveRecipients());

        /*
         * Third message (user2 -> user1)
         */
        $message3 = $this->createMessageMock($user2, $this->dates[2]);
        $thread->addMessage($message3);
        $thread->denormalize();

        $this->assertEquals($this->dates[2], $thread->getLastMessageDate());
        $this->assertEquals($this->dates[2], $thread->getMetadataForParticipant($user1)?->getLastMessageDate());
        $this->assertEquals($this->dates[2], $thread->getMetadataForParticipant($user2)?->getLastParticipantMessageDate());

        /*
         * Fourth message (user1 -> user2)
         */
        $message4 = $this->createMessageMock($user1, $this->dates[3]);
        $thread->addMessage($message4);
        $thread->denormalize();

        $this->assertEquals($this->dates[3], $thread->getLastMessageDate());
        $this->assertEquals($this->dates[3], $thread->getMetadataForParticipant($user1)?->getLastParticipantMessageDate());
        $this->assertEquals($this->dates[2], $thread->getMetadataForParticipant($user1)?->getLastMessageDate());
        $this->assertEquals($this->dates[3], $thread->getMetadataForParticipant($user2)?->getLastMessageDate());
        $this->assertEquals($this->dates[2], $thread->getMetadataForParticipant($user2)?->getLastParticipantMessageDate());

        $this->assertEquals('test thread subject hi dude', $thread->getKeywords());
        $this->assertFalse($thread->isDeletedByParticipant($user1));
    }

    private function createMessageMock(ParticipantInterface $sender, DateTimeImmutable $date)
    {
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();

        $message->method('getSender')
            ->willReturn($sender);
        $message->method('getCreatedAt')
            ->willReturn($date);
        $message->method('getTimestamp')
            ->willReturn($date->getTimestamp());
        $message->method('getBody')
            ->willReturn('hi dude');
        $message->method('setSpam')
            ->willReturnSelf();

        return $message;
    }

    private function createParticipantMock(int|string $id)
    {
        $user = $this->createMock(ParticipantInterface::class);
        $user->method('getId')
            ->willReturn($id);

        return $user;
    }
}

class TestThreadMetadata extends ThreadMetadata
{
}

class TestThread extends Thread
{
    public function __construct()
    {
        parent::__construct();
        $this->metadata = new ArrayCollection();
    }

    public function addParticipant(ParticipantInterface $participant): void
    {
        parent::addParticipant($participant);

        if (!$this->getMetadataForParticipant($participant)) {
            $meta = new TestThreadMetadata();
            $meta->setParticipant($participant);
            $this->metadata->add($meta);
        }
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }
}