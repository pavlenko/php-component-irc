<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerISON implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }
        $resp = [];
        foreach ($cmd->getArgs() as $arg) {
            if ($stor->sessions()->containsName($arg)) {
                $resp[] = $arg;
            }
        }
        return $sess->sendRPL(RPL::IS_ON, [], implode(' ', $resp));
    }
}