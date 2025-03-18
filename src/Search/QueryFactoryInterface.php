<?php

namespace FOS\ChatBundle\Search;

/**
 * Gets the search term from the request and prepares it.
 */
interface QueryFactoryInterface
{
    /**
     * Gets the search term.
     */
    public function createFromRequest() : Query;
}
