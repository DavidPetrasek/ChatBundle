<?php

namespace FOS\ChatBundle\SpamDetection;

class NoopSpamDetector implements SpamDetectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSpam($message): bool
    {
        return false;
    }
}
