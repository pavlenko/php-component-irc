<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    public function handleNAMES(CMD $cmd, SessionInterface $sess): void
    {
        //TODO optimize
        if ($cmd->numArgs() === 0) {
            foreach ($this->storage->channels() as $chan) {
                if (
                    !$chan->hasFlag(ChannelInterface::FLAG_PRIVATE) &&
                    !$chan->hasFlag(ChannelInterface::FLAG_SECRET)
                ) {
                    $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['= ' . $chan->getName()], $chan->getNamesAsString($this->storage));
                }
            }
            $names = [];
            foreach ($this->storage->sessions() as $user) {
                if (!$user->numChannels()) {
                    $names[] = $user->getNickname();
                }
            }
            $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['* *'], implode(' ', $names));
            $sess->sendRPL(RPL::RPL_END_OF_NAMES, ['*']);
        } else {
            $channels = array_filter(explode(',', $cmd->getArg(0)));
            foreach ($channels as $name) {
                $chan = $this->storage->channels()->searchByName($name);
                if (
                    null !== $chan &&
                    !$chan->hasFlag(ChannelInterface::FLAG_PRIVATE) &&
                    !$chan->hasFlag(ChannelInterface::FLAG_SECRET)
                ) {
                    $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['= ' . $chan->getName()], $chan->getNamesAsString($this->storage));
                    $sess->sendRPL(RPL::RPL_END_OF_NAMES, [$chan->getName()]);
                }
            }
        }
    }

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