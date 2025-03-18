<?php

namespace FOS\ChatBundle\Search;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gets the search term from the request and prepares it.
 */
class QueryFactory implements QueryFactoryInterface
{
    /**
     * Instanciates a new TermGetter.
     */
    public function __construct(
        private RequestStack|Request $request,
        /**
         * The query parameter containing the search term.
         */
        private string $queryParameter
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(): Query
    {
        $original = $this->getCurrentRequest()->query->get($this->queryParameter);
        $original = trim((string) $original);

        $escaped = $this->escapeTerm($original);

        return new Query($original, $escaped);
    }

    /**
     * Sets: the query parameter containing the search term.
     */
    public function setQueryParameter(string $queryParameter): void
    {
        $this->queryParameter = $queryParameter;
    }

    private function escapeTerm($term)
    {
        return $term;
    }

    /**
     * BC layer to retrieve the current request directly or from a stack.
     */
    private function getCurrentRequest() :?Request
    {
        if ($this->request instanceof Request) {
            return $this->request;
        }

        return $this->request->getCurrentRequest();
    }
}
