<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerPONG implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0 || $cmd->getArg(0) !== $stor->conf(Config::CFG_SERVER_NAME)) {
            return $sess->sendERR(ERR::NO_SUCH_SERVER, [$cmd->numArgs()]);
        }
        $sess->clrFlag(SessionInterface::FLAG_PINGING);
        return 0;
    }
}