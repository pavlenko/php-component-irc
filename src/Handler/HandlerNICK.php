<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerNICK implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if (empty($cmd->getArg(0))) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        if (!$stor->isValidSessionName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ERRONEOUS_NICKNAME, [$cmd->getArg(0)]);
        }

        if ($stor->sessions()->containsName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::NICKNAME_IN_USE, [$cmd->getArg(0)]);
        }

        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            foreach ($sess->getChannels($stor) as $channel) {
                foreach ($channel->sessions() as $user) {
                    $user->sendCMD($cmd->getCode(), [$cmd->getArg(0)], null, $sess->getPrefix());
                }
            }
            $stor->history()->addSession($sess);
        }

        $sess->setNickname($cmd->getArg(0));
        return 0;
    }
}