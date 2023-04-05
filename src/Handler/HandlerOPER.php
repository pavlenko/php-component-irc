<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerOPER implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 2) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $operators = (array) $stor->conf(Config::CFG_OPERATORS);
        if (count($operators) === 0) {
            return $sess->sendERR(ERR::ERR_NO_OPERATOR_HOST);
        }

        if (hash('sha256', (string) $cmd->getArg(1)) === ($operators[$cmd->getArg(0)] ?? null)) {
            return $sess->sendERR(ERR::ERR_PASSWORD_MISMATCH);
        }

        $sess->setFlag($sess::FLAG_IS_OPERATOR);
        $sess->sendRPL(RPL::RPL_YOU_ARE_OPERATOR);
        return 0;
    }
}