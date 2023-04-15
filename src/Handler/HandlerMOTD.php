<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerMOTD implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER);
        }

        $motd = $stor->conf(Config::CFG_MOTD_FILE);
        if (null !== $motd && is_readable($motd)) {
            $motd = file($motd, FILE_IGNORE_NEW_LINES) ?: null;
        }

        if (empty($motd)) {
            return $sess->sendERR(ERR::ERR_NO_MOTD);
        }

        $sess->sendRPL(RPL::RPL_MOTD_START, [], '- Message of the day -');
        foreach ($motd as $line) {
            $sess->sendRPL(RPL::RPL_MOTD, [], $line);
        }
        return $sess->sendRPL(RPL::RPL_END_OF_MOTD);
    }
}