<?php

namespace FOS\ChatBundle\Search;

use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Finds threads of a participant, matching a given query.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface FinderInterface
{
    /**
     * Finds threads of a participant, matching a given query.
     */
    public function find(Query $query) : array;

    /**
     * Finds threads of a participant, matching a given query.
     */
    public function getQueryBuilder(Query $query) : QueryBuilder;
}
