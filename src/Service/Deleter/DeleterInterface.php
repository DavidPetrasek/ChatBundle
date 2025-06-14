<?php

namespace FOS\ChatBundle\Service\Deleter;

use FOS\ChatBundle\Model\ReadableInterface;

/**
 * Marks readables as deleted.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface DeleterInterface
{
    /**
     * Marks the readable as deleted by the current authenticated user.
     */
    public function markAsDeleted(ReadableInterface $thread);

    /**
     * Marks the readable as undeleted by the current authenticated user.
     */
    public function markAsUndeleted(ReadableInterface $thread);
}
