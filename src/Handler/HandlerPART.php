<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerPART implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() == 0) {
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $channels = explode(',', $cmd->getArg(0));
        foreach ($channels as $channel) {
            $chan = $stor->channels()->searchByName($channel);

            if (null === $chan) {
                $sess->sendERR(ERR::NO_SUCH_CHANNEL, [$channel]);
            } elseif (!$chan->hasSession($sess)) {
                $sess->sendERR(ERR::NOT_ON_CHANNEL, [$chan->getName()]);
            } else {
                foreach ($chan->getSessions($stor) as $user) {
                    $user->sendCMD($cmd->getCode(), [$chan->getName()], $cmd->getComment(), $sess->getPrefix());
                }

                $chan->delSession($sess);
                $chan->delSpeaker($sess);
                $chan->delOperator($sess);

                $sess->delChannel($chan);

                if ($chan->numSessions() === 0) {
                    $stor->channels()->detach($chan);
                }
            }
        }
        return 0;
    }
}