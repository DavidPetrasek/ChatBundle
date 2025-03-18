<?php

namespace FOS\ChatBundle\FormModel;

abstract class AbstractMessage
{
    private string $body;

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
