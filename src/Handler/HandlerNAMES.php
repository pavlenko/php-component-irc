<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerNAMES implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() !==0) {
            $channels = [];
            $selected = array_filter(explode(',', $cmd->getArg(0)));
            foreach ($stor->channels() as $chan) {
                if (in_array($chan->getName(), $selected)) {
                    $channels[] = $chan;
                }
            }
        } else {
            $channels = $stor->channels();
        }

        foreach ($channels as $chan) {
            if (!$chan->hasFlag(ChannelInterface::FLAG_PRIVATE) && !$chan->hasFlag(ChannelInterface::FLAG_SECRET)) {
                $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['= ' . $chan->getName()], $chan->getNamesAsString($stor));
                if ($cmd->numArgs() !== 0) {
                    $sess->sendRPL(RPL::RPL_END_OF_NAMES, [$chan->getName()]);
                }
            }
        }

        if ($cmd->numArgs() === 0) {
            $names = [];
            foreach ($stor->sessions() as $user) {
                if (!$user->numChannels()) {
                    $names[] = $user->getNickname();
                }
            }
            $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['* *'], implode(' ', $names));
            $sess->sendRPL(RPL::RPL_END_OF_NAMES, ['*']);
        }
        return 0;
    }
}
