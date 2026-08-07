<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Service\SpamDetection;

use FOS\ChatBundle\Model\SpamStatus;

/**
 * Tells whether or not a new message looks like spam.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SpamDetectorInterface
{
    public function check(string $message): SpamStatus;
}
