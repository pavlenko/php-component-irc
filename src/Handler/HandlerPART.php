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
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $channels = explode(',', $cmd->getArg(0));
        foreach ($channels as $channel) {
            $chan = $stor->channels()->searchByName($channel);

            if (null === $chan) {
                $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$channel]);
            } elseif (!$chan->sessions()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
            } else {
                foreach ($chan->sessions() as $user) {
                    $user->sendCMD($cmd->getCode(), [$chan->getName()], $cmd->getComment(), $sess->getPrefix());
                }

                $chan->sessions()->detach($sess);
                $chan->speakers()->detach($sess);
                $chan->operators()->detach($sess);
                $sess->channels()->detach($chan);

                if (count($chan->sessions()) === 0) {
                    $stor->channels()->detach($chan);
                }
            }
        }
        return 0;
    }
}