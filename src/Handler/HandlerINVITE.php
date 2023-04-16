<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerINVITE implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() < 2) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $user = $stor->sessions()->searchByName($cmd->getArg(0));
        $chan = $stor->channels()->searchByName($cmd->getArg(1));
        if (null === $user) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
        }

        if (null === $chan || !$chan->hasSession($sess)) {
            return $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
        }

        if ($chan->hasSession($sess)) {
            return $sess->sendERR(ERR::ERR_USER_ON_CHANNEL, [$chan->getName()]);
        }

        if ($chan->hasFlag(ChannelInterface::FLAG_INVITE_ONLY) && !$chan->hasOperator($sess)) {
            return $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
        }

        $chan->addInvited($user);
        $user->sendCMD(CMD::CMD_INVITE, [$user->getNickname()], $chan->getName(), $sess->getPrefix());
        $sess->sendRPL(RPL::RPL_INVITING, [$chan->getName(), $user->getNickname()]);
        if ($user->hasFlag(SessionInterface::FLAG_AWAY)) {
            $sess->sendRPL(RPL::RPL_AWAY, [$user->getNickname()], $user->getAwayMessage());
        }
        return 0;
    }
}