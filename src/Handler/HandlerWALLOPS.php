<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerWALLOPS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
            return $sess->sendERR(ERR::NO_PRIVILEGES);
        }
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }
        foreach ($stor->sessions() as $user) {
            if ($user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
                $user->sendCMD($cmd->getCode(), [], $cmd->getArg(0));
            }
        }
        return 0;
    }
}