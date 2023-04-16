<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\Event;
use PE\Component\IRC\RPL;
use PE\Component\IRC\Server;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerREHASH implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            return $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        }

        $stor->trigger(Server::EVT_REHASH, $event = new Event());
        return $sess->sendRPL(RPL::RPL_REHASHING, [$event->getPayload()]);
    }
}