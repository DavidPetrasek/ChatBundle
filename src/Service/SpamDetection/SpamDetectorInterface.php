<?php

namespace FOS\ChatBundle\Service\SpamDetection;

/**
 * Tells whether or not a new message looks like spam.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SpamDetectorInterface
{
    /**
     * Tells whether or not a new message looks like spam.
     */
    public function isSpam($message) : bool;
}
