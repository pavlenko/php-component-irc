<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * SQUIT <server> :<comment>
 * </code>
 */
class HandlerSQUIT implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0 || $cmd->getComment() === null) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]);
        }

        if (!$sess->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
            //TODO also check if session is client
            return $sess->sendERR(ERR::ERR_NO_PRIVILEGES, [$sess->getNickname()]);
        }

        $serv = $stor->sessions()->searchByName($cmd->getArg(0));
        if (null === $serv) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname(), $cmd->getArg(0)]);
        }

        $serv->close();
        foreach ($stor->sessions(1) as $serv) {
            $serv->sendCMD($cmd->getCode(), $cmd->getArgs(), $cmd->getComment(), $sess->getNickname());
        }
        return 0;
    }
}
