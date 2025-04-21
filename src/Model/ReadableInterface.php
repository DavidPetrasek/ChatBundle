<?php

namespace FOS\ChatBundle\Model;

interface ReadableInterface
{
    /**
     * Tells if this is read by this participant.
     */
    public function isReadByParticipant(ParticipantInterface $participant) : bool;

    /**
     * Sets whether or not this participant has read this.
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, bool $isRead);

    /**
     * Sets whether or not this participant has deleted this.
     */
    public function setIsDeletedByParticipant(ParticipantInterface $participant, bool $isDeleted);
}
