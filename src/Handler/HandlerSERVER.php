<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

class HandlerSERVER implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)/*TODO also check server by name*/) {
            return $sess->sendERR(ERR::ALREADY_REGISTERED, [$cmd->getArg(0)]);
        }

        if ($cmd->numArgs() < 3 || empty($cmd->getComment())) {
            $sess->close();
            return $sess->sendCMD(CMD::ERROR, [], 'Need more params');
        }

        $sess->set('servername', $cmd->getArg(0));
        $sess->set('hop_count', $cmd->getArg(1));
        $sess->set('token', $cmd->getArg(2));
        $sess->set('info', $cmd->getComment());

        //TODO reply with pass and server
        //TODO add to stor servers
        //TODO notify others
        return 0;
    }
}
