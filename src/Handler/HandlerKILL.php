<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerKILL implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
            return $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        }

        if ($cmd->numArgs() === 0 || empty($cmd->getComment())) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        if ($stor->conf(Config::CFG_SERVER_NAME) === $cmd->getArg(0)) {
            return $sess->sendERR(ERR::ERR_CANNOT_KILL_SERVER);
        }

        $user = $stor->sessions()->searchByName($cmd->getArg(0));
        if (null === $user) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
        }

        //TODO check what response needed
        //$user->sendCMD('', [], $cmd->getComment());
        $user->sendCMD($cmd->getCode(), [$user->getNickname()], $cmd->getComment(), $user->getPrefix());
        $user->close();
        return 0;
    }
}