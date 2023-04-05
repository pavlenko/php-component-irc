<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerQUIT implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() > 0) {
            $sess->setQuitMessage(implode(' ', $cmd->getArgs()));
        }
        foreach ($sess->getChannels($stor) as $chan) {
            $chan->delSession($sess);
            $chan->delSpeaker($sess);
            $chan->delOperator($sess);

            $sess->delChannel($chan);
            if ($chan->numSessions() === 0) {
                $stor->channels()->detach($chan);
            }
        }
        $stor->history()->addSession($sess);
        $sess->close();
        return 0;
    }
}