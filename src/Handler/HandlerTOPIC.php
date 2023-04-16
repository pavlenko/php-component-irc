<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerTOPIC implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 1) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $chan = $stor->channels()->searchByName($cmd->getArg(0));
        if (null === $chan || !$chan->hasSession($sess)) {
            return $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$cmd->getCode()]);
        }

        if ($cmd->numArgs() < 2) {
            return !empty($chan->getTopic())
                ? $sess->sendRPL(RPL::RPL_TOPIC, [$cmd->getArg(0)], $chan->getTopic())
                : $sess->sendRPL(RPL::RPL_NO_TOPIC, [$cmd->getArg(0)]);
        }

        if ($chan->hasFlag(ChannelInterface::FLAG_TOPIC_SET) && !$chan->hasOperator($sess)) {
            return $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$cmd->getArg(0)]);
        }

        $chan->setTopic($cmd->getArg(1));
        foreach ($chan->getSessions($stor) as $user) {
            $user->sendCMD(CMD::CMD_TOPIC, [$cmd->getArg(0)], $cmd->getArg(1));
        }
        return 0;
    }
}