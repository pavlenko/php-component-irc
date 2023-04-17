<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * CONNECT <target_server> [<port> [<remote_server>]]
 */
class HandlerCONNECT implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]);
        }

        if (!$sess->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
            return $sess->sendERR(ERR::ERR_NO_PRIVILEGES, [$sess->getNickname()]);
        }

        if (!isset($stor->conf('servers')[$cmd->getArg(0)])) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(0)]);
        }

        $stor->trigger('connect', ...$cmd->getArgs());
        return 0;
    }
}
