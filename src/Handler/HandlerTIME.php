<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerTIME implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        return ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername())
            ? $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(0)])
            : $sess->sendRPL(RPL::RPL_TIME, [$sess->getServername()], date(Config::DEFAULT_DATETIME_FORMAT));
    }
}