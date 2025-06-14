<?php
namespace FOS\ChatBundle\Service\Composer;

use FOS\ChatBundle\Service\MessageBuilder\AbstractMessageBuilder;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Factory for message builders.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ComposerInterface
{
    /**
     * Starts composing a message, starting a new thread.
     */
    public function newThread() : AbstractMessageBuilder;

    /**
     * Starts composing a message in a reply to a thread.
     */
    public function reply(ThreadInterface $thread) : AbstractMessageBuilder;
}
