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

    /**
     * This method should be setUp(): void
     * For compatibility reasons with old versions of PHP, we cannot use neither setUp(): void nor setUp().
     */
    public function setUpBeforeTest(): void
    {
        $this->participantProvider = $this->getMockBuilder(\FOS\ChatBundle\Security\ParticipantProviderInterface::class)->getMock();
        $this->provider = $this->getMockBuilder(\FOS\ChatBundle\Service\Provider\ProviderInterface::class)->getMock();
        $this->authorizer = $this->getMockBuilder(\FOS\ChatBundle\Security\AuthorizerInterface::class)->getMock();
        $this->participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)->getMock();
        $this->extension = new MessageExtension($this->participantProvider, $this->provider, $this->authorizer);
    }

    public function testIsReadReturnsTrueWhenRead(): void
    {
        $this->setUpBeforeTest();

        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $readAble = $this->getMockBuilder(\FOS\ChatBundle\Model\ReadableInterface::class)->getMock();
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->will($this->returnValue(true));
        $this->assertTrue($this->extension->isRead($readAble));
    }

    public function testIsReadReturnsFalseWhenNotRead(): void
    {
        $this->setUpBeforeTest();

        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $readAble = $this->getMockBuilder(\FOS\ChatBundle\Model\ReadableInterface::class)->getMock();
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->will($this->returnValue(false));
        $this->assertFalse($this->extension->isRead($readAble));
    }

    public function testCanDeleteThreadWhenHasPermission(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->will($this->returnValue(true));
        $this->assertTrue($this->extension->canDeleteThread($thread));
    }

    public function testCanDeleteThreadWhenNoPermission(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->will($this->returnValue(false));
        $this->assertFalse($this->extension->canDeleteThread($thread));
    }

    public function testIsThreadDeletedByParticipantWhenDeleted(): void
    {
        $this->setUpBeforeTest();

        $thread = $this->getThreadMock();
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $thread->expects($this->once())->method('isDeletedByParticipant')->with($this->participant)->will($this->returnValue(true));
        $this->assertTrue($this->extension->isThreadDeletedByParticipant($thread));
    }

    public function testGetNbUnreadCacheStartsEmpty(): void
    {
        $this->setUpBeforeTest();

        /*
         * assertAttributeEmpty is deprecated, see deprecated https://github.com/sebastianbergmann/phpunit/issues/3338
         */
//        if (\method_exists($this, 'assertAttributeEmpty')) {
//            $this->assertAttributeEmpty('nbUnreadMessagesCache', $this->extension);
//        }
        $this->assertEmpty($this->extension->getNbUnread());
        $this->extension->getNbUnread();
    }

    public function testGetNbUnread(): void
    {
        $this->setUpBeforeTest();

        /*
         * assertAttributeEmpty is deprecated, see deprecated https://github.com/sebastianbergmann/phpunit/issues/3338
         */
//        if (\method_exists($this, 'assertAttributeEmpty')) {
//            $this->assertAttributeEmpty('nbUnreadMessagesCache', $this->extension);
//        }
        $this->assertEmpty($this->extension->getNbUnread());
        $this->provider->expects($this->once())->method('getNbUnreadMessages')->will($this->returnValue(3));
        $this->assertEquals(3, $this->extension->getNbUnread());
    }

    public function testGetNbUnreadStoresCache(): void
    {
        $this->setUpBeforeTest();

        $this->provider->expects($this->once())->method('getNbUnreadMessages')->will($this->returnValue(3));
        //we call it twice but expect to only get one call
        $this->extension->getNbUnread();
        $this->extension->getNbUnread();
    }

    public function testGetName(): void
    {
        $this->setUpBeforeTest();

        $this->assertEquals('fos_chat', $this->extension->getName());
    }

    private function getThreadMock()
    {
        return $this->getMockBuilder(\FOS\ChatBundle\Model\ThreadInterface::class)->getMock();
    }
}
