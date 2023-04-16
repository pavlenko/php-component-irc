<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    public function handleLIST(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 1 && $cmd->getArg(1) !== $sess->getServername()) {
            $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(1)]);
        } else {
            $names = $cmd->numArgs() > 0 ? array_filter(explode(',', $cmd->getArg(0))) : [];

            $sess->sendRPL(RPL::RPL_LIST_START, ['Channel'], 'Users Name');
            foreach ($this->storage->channels() as $chan) {
                if (!empty($names) && !in_array($chan->getName(), $names)) {
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
                //TODO filter users by visible flag???
                $sess->sendRPL(RPL::RPL_LIST, [$name, $chan->numSessions()], $info ?? null);
            }
            $sess->sendRPL(RPL::RPL_LIST_END);
        }
    }
}