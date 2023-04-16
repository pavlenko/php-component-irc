<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerUSERHOST implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $resp = [];
        foreach ($cmd->getArgs() as $arg) {
            if ($user = $stor->sessions()->searchByName($arg)) {
                $resp[] = $arg
                    . ($user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR) ? '*' : '')
                    . '='
                    . ($user->hasFlag(SessionInterface::FLAG_AWAY) ? '-' : '+')
                    . $user->getUsername()
                    . '@'
                    . $user->getHostname();
            }
        }
        return $sess->sendRPL(RPL::RPL_USER_HOST, [], implode(' ', $resp));
    }
}