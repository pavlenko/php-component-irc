<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerINFO implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            return $sess->sendERR(ERR::NO_SUCH_SERVER, [$cmd->getArg(0)]);
        }
        $sess->sendRPL(RPL::INFO, [], $stor->conf(Config::CFG_INFO));
        $sess->sendRPL(RPL::END_OF_INFO);
        return 0;
    }
}
