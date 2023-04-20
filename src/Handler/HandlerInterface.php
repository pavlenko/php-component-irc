<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

interface HandlerInterface
{
    //TODO move $stor to internal property and pass from constructor
    //TODO add $events internal property and pass from constructor
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int;
}
