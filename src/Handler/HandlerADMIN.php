<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerADMIN implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]);
        }

        $sess->sendRPL(RPL::RPL_ADMIN_ME, [$sess->getServername()]);
        $sess->sendRPL(RPL::RPL_ADMIN_LOC1, [$stor->conf(Config::CFG_ADMIN_LOCATION1)]);
        $sess->sendRPL(RPL::RPL_ADMIN_LOC2, [$stor->conf(Config::CFG_ADMIN_LOCATION2)]);
        $sess->sendRPL(RPL::RPL_ADMIN_ME, [$stor->conf(Config::CFG_ADMIN_EMAIL)]);
        return 0;
    }
}