<?php

namespace FOS\ChatBundle\Tests\Twig\Extension;

use FOS\ChatBundle\Twig\Extension\MessageExtension;
use PHPUnit\Framework\TestCase;

/**
 * Testfile for MessageExtension.
 */
class MessageExtensionTest extends TestCase
{
    private ?\FOS\ChatBundle\Twig\Extension\MessageExtension $extension = null;

    private $participantProvider;

    private $provider;

    private $authorizer;

    private $participant;

    public function setUp(): void
    {
        $this->participantProvider = $this->getMockBuilder(\FOS\ChatBundle\Security\ParticipantProviderInterface::class)->getMock();
        $this->provider = $this->getMockBuilder(\FOS\ChatBundle\Service\Provider\ProviderInterface::class)->getMock();
        $this->authorizer = $this->getMockBuilder(\FOS\ChatBundle\Security\AuthorizerInterface::class)->getMock();
        $this->participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)->getMock();
        $this->extension = new MessageExtension($this->participantProvider, $this->provider, $this->authorizer);
    }

    public function testReadReturnsTrueWhenRead(): void
    {
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->willReturn($this->participant);
        $readAble = $this->getMockBuilder(\FOS\ChatBundle\Model\ReadableInterface::class)->getMock();
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->willReturn(true);
        $this->assertTrue($this->extension->isRead($readAble));
    }

    public function testReadReturnsFalseWhenNotRead(): void
    {
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->willReturn($this->participant);
        $readAble = $this->getMockBuilder(\FOS\ChatBundle\Model\ReadableInterface::class)->getMock();
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->willReturn(false);
        $this->assertFalse($this->extension->isRead($readAble));
    }

    public function testCanDeleteThreadWhenHasPermission(): void
    {
        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->willReturn(true);
        $this->assertTrue($this->extension->canDeleteThread($thread));
    }

    public function testCanDeleteThreadWhenNoPermission(): void
    {
        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->willReturn(false);
        $this->assertFalse($this->extension->canDeleteThread($thread));
    }

    public function testIsThreadDeletedByParticipantWhenDeleted(): void
    {
        $thread = $this->getThreadMock();
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->willReturn($this->participant);
        $thread->expects($this->once())->method('isDeletedByParticipant')->with($this->participant)->willReturn(true);
        $this->assertTrue($this->extension->isThreadDeletedByParticipant($thread));
    }

    public function testGetNbUnreadCacheStartsEmpty(): void
    {
        $this->assertEmpty($this->extension->getNbUnread());
    }

    public function testGetNbUnread(): void
    {
        $this->provider->expects($this->once())->method('getNbUnreadMessages')->willReturn(3);

        // first call fetches from provider and caches 3
        $this->assertEquals(3, $this->extension->getNbUnread());

        // second call reads from cache; provider not called again
        $this->assertEquals(3, $this->extension->getNbUnread());
    }

    public function testGetNbUnreadStoresCache(): void
    {
        $this->provider->expects($this->once())->method('getNbUnreadMessages')->willReturn(3);
        //we call it twice but expect to only get one call
        $this->extension->getNbUnread();
        $this->extension->getNbUnread();
    }

    public function testGetName(): void
    {
        $this->assertEquals('fos_chat', $this->extension->getName());
    }

    private function getThreadMock()
    {
        return $this->getMockBuilder(\FOS\ChatBundle\Model\ThreadInterface::class)->getMock();
    }
}
