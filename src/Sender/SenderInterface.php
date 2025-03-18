<?php

namespace FOS\ChatBundle\Sender;

use FOS\ChatBundle\Model\MessageInterface;

/**
 * Sends messages.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SenderInterface
{
    /**
     * Sends the given message.
     */
    public function send(MessageInterface $message);
}
