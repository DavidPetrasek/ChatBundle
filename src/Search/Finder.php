<?php

namespace FOS\ChatBundle\Search;

use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\ModelManager\ThreadManagerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;

/**
 * Finds threads of a participant, matching a given query.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Finder implements FinderInterface
{
    public function __construct(
        /**
         * The participant provider instance.
         */
        private readonly ParticipantProviderInterface $participantProvider,
        /**
         * The thread manager.
         */
        private readonly ThreadManagerInterface $threadManager
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function find(Query $query): array
    {
        return $this->threadManager->findParticipantThreadsBySearch($this->getAuthenticatedParticipant(), $query->getEscaped());
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(Query $query): QueryBuilder
    {
        return $this->threadManager->getParticipantThreadsBySearchQueryBuilder($this->getAuthenticatedParticipant(), $query->getEscaped());
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
