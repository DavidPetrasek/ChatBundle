<?php

namespace FOS\ChatBundle\ModelManager;

use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Abstract Thread Manager implementation which can be used as base class by your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class ThreadManager implements ThreadManagerInterface
{
    /**
     * Creates an empty comment thread instance.
     */
    public function createThread() : ThreadInterface
    {
        $class = $this->getClass();

        return new $class();
    }
}
