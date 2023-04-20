<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerVERSION implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        return ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername())
            ? $sess->sendERR(ERR::NO_SUCH_SERVER, [$cmd->getArg(0)])
            : $sess->sendRPL(RPL::VERSION, [
                $stor->conf(Config::CFG_VERSION_NUMBER) . '.' . $stor->conf(Config::CFG_VERSION_DEBUG),
                $stor->conf(Config::CFG_SERVER_NAME)
            ], $stor->conf(Config::CFG_VERSION_COMMENT) ?: null);
    }
}