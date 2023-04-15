<?php

namespace PE\Component\IRC;

final class Event
{
    /**
     * @var mixed Depends on context
     */
    private $payload;

    public function __construct($payload = null)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }
}