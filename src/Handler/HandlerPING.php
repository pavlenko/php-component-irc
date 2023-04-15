<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerPING implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        return $cmd->numArgs() === 0
            ? $sess->sendERR(ERR::ERR_NO_ORIGIN)
            : $sess->sendCMD(CMD::CMD_PONG, [], $cmd->getArg(0), $sess->getServername());
    }
}