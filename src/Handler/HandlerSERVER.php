<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * SERVER <servername> <hop_count> <token> :<info>
 * SERVER <servername> <pass> <hop_count> <id> :<info>
 * </code>
 */
class HandlerSERVER implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 3) {
            $sess->close();
            return $sess->sendCMD(CMD::ERROR, [], 'Need more params');
        }

        if ($stor->sessions()->searchByName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ALREADY_REGISTERED, [$cmd->getArg(0)]);
        }

        $sess->setType(SessionInterface::TYPE_SERVER);
        $sess->__set('servername', $cmd->getArg(0));
        $sess->__set('hop_count', $cmd->getArg(1));
        $sess->__set('token', $cmd->getArg(2));
        $sess->__set('info', $cmd->getComment());

        foreach ($stor->sessions() as $serv) {
            $serv->sendCMD(CMD::SERVER, $cmd->getArgs(), $cmd->getComment(), $sess->getServername());
        }
        return 0;
    }
}
