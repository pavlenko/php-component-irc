<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerAWAY implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            $sess->clrFlag(SessionInterface::FLAG_AWAY);
            $sess->sendRPL(RPL::UN_AWAY);
        } else {
            $sess->setFlag(SessionInterface::FLAG_AWAY);
            $sess->setAwayMessage(implode(' ', $cmd->getArgs()));
            $sess->sendRPL(RPL::NOW_AWAY);
        }
        return 0;
    }
}