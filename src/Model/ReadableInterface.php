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
    public function setReadByParticipant(ParticipantInterface $participant, bool $read);

    /**
     * Sets whether or not this participant has deleted this.
     */
    public function setDeletedByParticipant(ParticipantInterface $participant, bool $deleted);
}
