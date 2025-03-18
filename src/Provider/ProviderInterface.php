<?php

namespace FOS\ChatBundle\Provider;

use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Provides threads for the current authenticated user.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Gets the thread in the inbox of the current user.
     */
    public function getInboxThreads() : array;

    /**
     * Gets the thread in the sentbox of the current user.
     */
    public function getSentThreads() : array;

    /**
     * Gets the deleted threads of the current user.
     */
    public function getDeletedThreads() : array;

    /**
     * Gets a thread by its ID
     * Performs authorization checks
     * Marks the thread as read.
     */
    public function getThread($threadId) : ThreadInterface;

    /**
     * Tells how many unread messages the authenticated participant has.
     */
    public function getNbUnreadMessages() : int;
}
