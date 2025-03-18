<?php

namespace FOS\ChatBundle\SpamDetection;

use FOS\ChatBundle\FormModel\NewThreadMessage;

class NoopSpamDetector implements SpamDetectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSpam(NewThreadMessage $message): bool
    {
        return false;
    }
}
