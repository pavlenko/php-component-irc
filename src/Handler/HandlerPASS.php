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
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return $sess->sendERR(ERR::ALREADY_REGISTERED);
        }

        if ($cmd->numArgs() > 1) {
            // from server
            if ($cmd->numArgs() < 3) {
                return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
            }
            $sess->set('password', $cmd->getArg(0));
            $sess->set('version', $cmd->getArg(1));
            $sess->set('flags', $cmd->getArg(2));
            $sess->set('options', $cmd->getArg(3));
        } elseif ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $sess->setPassword($cmd->getArg(0));
        }
        return 0;
    }
}
