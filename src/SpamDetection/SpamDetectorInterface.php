<?php

namespace FOS\ChatBundle\SpamDetection;

use FOS\ChatBundle\FormModel\NewThreadMessage;

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
    public function isSpam(NewThreadMessage $message) : bool;
}
