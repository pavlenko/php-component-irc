<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * ERROR :<message>
 * -> ERROR :<message> ;Error to other server
 * -> NOTICE <user> :<message> ;Notice operator about error
 * </code>
 */
class HandlerERROR implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        foreach ($stor->sessions() as $user) {
            if (!$user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
                continue;
            }
            $user->sendCMD(CMD::NOTICE, [$user->getNickname()], $cmd->getComment());
        }
        return 0;
    }
}
