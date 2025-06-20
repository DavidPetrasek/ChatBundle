<?php

namespace FOS\ChatBundle\Service\Reader;

use FOS\ChatBundle\Model\ReadableInterface;

/**
 * Marks messages and threads as read or unread.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ReaderInterface
{
    /**
     * Marks the readable as read by the current authenticated user.
     */
    public function markAsRead(ReadableInterface $readable);

    /**
     * Marks the readable as unread by the current authenticated user.
     */
    public function markAsUnread(ReadableInterface $readable);
}
