<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerPASS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return $sess->sendERR(ERR::ERR_ALREADY_REGISTERED);
        }
        $sess->setPassword($cmd->getArg(0));
        return 0;
    }
}