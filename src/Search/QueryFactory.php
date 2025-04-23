<?php

namespace FOS\ChatBundle\Search;

use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Gets the search term from the request and prepares it.
 */
class QueryFactory implements QueryFactoryInterface
{
    public function __construct
    (
        private readonly RequestStack $requestStack,
        /**
         * The query parameter containing the search term.
         */
        private string $queryParameter
    )
    {}

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(): Query
    {
        $original = $this->requestStack->getCurrentRequest()->query->get($this->queryParameter);
        $original = trim((string) $original);

        return new Query($original);
    }

    /**
     * Sets: the query parameter containing the search term.
     */
    public function setQueryParameter(string $queryParameter): self
    {
        $this->queryParameter = $queryParameter;

        return $this;
    }
}
