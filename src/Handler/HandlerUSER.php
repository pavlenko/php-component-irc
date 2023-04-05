<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ConnectionInterface;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerUSER implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if (count($cmd->getArgs()) < 3 || empty($cmd->getComment())) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return $sess->sendERR(ERR::ERR_ALREADY_REGISTERED);
        }

        if (empty($sess->getNickname())) {
            return $sess->sendERR(ERR::ERR_NO_NICKNAME_GIVEN);
        }

        $pass = $stor->conf(Config::CFG_PASSWORD);
        if (!empty($pass) && $pass !== $sess->getPassword()) {
            return $sess->sendERR(ERR::ERR_PASSWORD_MISMATCH);
        }

        $sess->setUsername($cmd->getArg(0));
        $sess->setRealname($cmd->getComment());

        $sess->setFlag(SessionInterface::FLAG_REGISTERED);
        $sess->sendRPL(RPL::RPL_WELCOME);
        $sess->sendRPL(RPL::RPL_YOUR_HOST, [], sprintf(
            "Your host is %s, running version %s",
            $stor->conf(Config::CFG_SERVER_NAME),
            $stor->conf(Config::CFG_VERSION_NUMBER)
        ));
        $sess->sendRPL(RPL::RPL_CREATED, [], "This server was created {$stor->conf(Config::CFG_CREATED_AT)}");
        $sess->sendRPL(RPL::RPL_MY_INFO, [
            $stor->conf(Config::CFG_SERVER_NAME),
            $stor->conf(Config::CFG_VERSION_NUMBER),
            implode(['i', 'o', 's', 'w']),
            implode(['b', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 's', 't', 'v']),
            implode(['b', 'k', 'l', 'o', 'v']),
        ]);

        // trigger event with command because this is only one way to communicate with daemon
        $stor->trigger(ConnectionInterface::EVT_INPUT, new CMD(CMD::CMD_MOTD, [$sess->getServername()]), $sess);
        return 0;
    }
}