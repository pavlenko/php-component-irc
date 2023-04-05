<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

interface HandlerInterface
{
    public function __invoke(CMD $CMD, SessionInterface $sess, StorageInterface $stor): void;
}