<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerLIST implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() > 1 && $cmd->getArg(1) !== $sess->getServername()) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(1)]);
        }

        $selected = $cmd->numArgs() > 0 ? array_filter(explode(',', $cmd->getArg(0))) : [];

        $sess->sendRPL(RPL::RPL_LIST_START, ['Channel'], 'Users Name');
        foreach ($stor->channels() as $chan) {
            if (!empty($selected) && !in_array($chan->getName(), $selected)) {
                continue;
            }
            if ($chan->hasFlag(ChannelInterface::FLAG_SECRET) && !$chan->hasSession($sess)) {
                continue;
            }
            if ($chan->hasFlag(ChannelInterface::FLAG_PRIVATE) && !$chan->hasSession($sess)) {
                $name = '*';
            } else {
                $name  = $chan->getName();
                $info  = '[' . $chan->getFlagsAsString() . '] ' . $chan->getTopic();
            }
            $sess->sendRPL(RPL::RPL_LIST, [$name, $chan->numSessions()], $info ?? null);
        }
        return $sess->sendRPL(RPL::RPL_LIST_END);
    }
}
