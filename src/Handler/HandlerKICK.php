<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerKICK implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 2) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $chan = $stor->channels()->searchByName($cmd->getArg(0));
        if (null === $chan) {
            return $sess->sendERR(ERR::NO_SUCH_CHANNEL, [$cmd->getArg(0)]);
        }

        if (!$chan->hasSession($sess)) {
            return $sess->sendERR(ERR::NOT_ON_CHANNEL, [$chan->getName()]);
        }

        if (!$chan->hasOperator($sess)) {
            return $sess->sendERR(ERR::OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
        }

        $user = $stor->sessions()->searchByName($cmd->getArg(1));
        if (null === $user) {
            return $sess->sendERR(ERR::NO_SUCH_NICK, [$cmd->getArg(1)]);
        }

        if (!$chan->hasSession($user)) {
            $sess->sendERR(ERR::USER_NOT_IN_CHANNEL, [$cmd->getArg(1), $cmd->getArg(0)]);
        }

        foreach ($chan->getSessions($stor) as $s) {
            $s->sendCMD(
                $cmd->getCode(),
                [$chan->getName(), $user->getNickname()],
                $cmd->numArgs() > 2 ? $cmd->getArg(2) : $sess->getNickname()
            );
        }
        $chan->delSession($user);
        $chan->delSpeaker($user);
        $chan->delOperator($user);
        $user->delChannel($chan);
        return 0;
    }
}