<?php

namespace PE\Component\IRC;

interface HandlerInterface
{
    public function __invoke(CMD $CMD, SessionInterface $sess, StorageInterface $stor): void;
}