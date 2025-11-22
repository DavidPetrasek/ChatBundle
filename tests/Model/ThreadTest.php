<?php

namespace FOS\ChatBundle\Tests\Model;

use FOS\ChatBundle\Model\ParticipantInterface;
use PHPUnit\Framework\TestCase;
use FOS\ChatBundle\Model\Thread as ModelThread;

class ThreadTest extends TestCase
{
    public function testGetOtherParticipants(): void
    {
        $u1 = $this->createParticipantMock(1);
        $u2 = $this->createParticipantMock(2);
        $u3 = $this->createParticipantMock(3);

        $thread = $this->getMockBuilder(ModelThread::class)
            ->onlyMethods(['getParticipants', 'isParticipant', 'addParticipant', 'getAllMetadata'])
            ->getMock();

        $thread->expects($this->atLeastOnce())
            ->method('getParticipants')
            ->willReturn([$u1, $u2, $u3]);

        $toIds = (fn(array $participants): array => array_map(fn(ParticipantInterface $participant) => $participant->getId(), $participants));

        $this->assertSame($toIds([$u2, $u3]), $toIds($thread->getOtherParticipants($u1)));
        $this->assertSame($toIds([$u1, $u3]), $toIds($thread->getOtherParticipants($u2)));
        $this->assertSame($toIds([$u1, $u2]), $toIds($thread->getOtherParticipants($u3)));
    }

    private function createParticipantMock($id)
    {
        $participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)
            ->disableOriginalConstructor(true)
            ->getMock();

        $participant->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $participant;
    }
}
