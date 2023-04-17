<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * SERVER <servername> <hop_count> <token> :<info>
 * </code>
 */
class HandlerSERVER implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 3) {
            $sess->close();
            return $sess->sendCMD(CMD::CMD_ERROR, [], 'Need more params');
        }

        if ($stor->sessions()->searchByName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ERR_ALREADY_REGISTERED, [$cmd->getArg(0)]);
        }

        $sess->setType(SessionInterface::TYPE_SERVER);
        $sess->set('servername', $cmd->getArg(0));
        $sess->set('hop_count', $cmd->getArg(1));
        $sess->set('token', $cmd->getArg(2));
        $sess->set('info', $cmd->getComment());

        foreach ($stor->sessions() as $serv) {
            $serv->sendCMD(CMD::CMD_SERVER, $cmd->getArgs(), $cmd->getComment(), $sess->getServername());
        }
        return 0;
    }
}