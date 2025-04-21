<?php

namespace FOS\ChatBundle\Search;

/**
 * Search term.
 */
class Query implements \Stringable
{
    public function __construct(private string $term)
    {
    }

    public function getTerm() : string
    {
        return $this->term;
    }

    public function setTerm(string $term): void
    {
        $this->term = $term;
    }

    /**
     * Converts to the term string.
     */
    public function __toString(): string
    {
        return $this->getTerm();
    }

    public function isEmpty(): bool
    {
        return empty($this->term);
    }
}
