<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\EventsInterface;
use PE\Component\IRC\StorageInterface;

abstract class HandlerBase implements HandlerInterface
{
    protected StorageInterface $storage;
    protected EventsInterface  $emitter;

    public function __construct(StorageInterface $storage, EventsInterface $emitter)
    {
        $this->storage = $storage;
        $this->emitter = $emitter;
    }
}
