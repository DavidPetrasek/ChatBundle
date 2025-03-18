<?php

namespace FOS\ChatBundle\Search;

/**
 * Search term.
 */
class Query implements \Stringable
{
    public function __construct(private string $original, private string $escaped)
    {
    }

    public function getOriginal() : string
    {
        return $this->original;
    }

    public function setOriginal(string $original): void
    {
        $this->original = $original;
    }

    public function getEscaped() : string
    {
        return $this->escaped;
    }

    public function setEscaped(string $escaped): void
    {
        $this->escaped = $escaped;
    }

    /**
     * Converts to the original term string.
     */
    public function __toString(): string
    {
        return $this->getOriginal();
    }

    public function isEmpty(): bool
    {
        return empty($this->original);
    }
}
