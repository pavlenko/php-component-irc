<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    public function handleTOPIC(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $chan = $this->storage->channels()->searchByName($cmd->getArg(0));
            if (null === $chan || !$chan->hasSession($sess)) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$cmd->getCode()]);
            } elseif ($cmd->numArgs() < 2) {
                if (!empty($chan->getTopic())) {
                    $sess->sendRPL(RPL::RPL_TOPIC, [$cmd->getArg(0)], $chan->getTopic());
                } else {
                    $sess->sendRPL(RPL::RPL_NO_TOPIC, [$cmd->getArg(0)]);
                }
            } else {
                if ($chan->hasFlag(ChannelInterface::FLAG_TOPIC_SET) && !$chan->hasOperator($sess)) {
                    $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$cmd->getArg(0)]);
                } else {
                    $chan->setTopic($cmd->getArg(1));
                    foreach ($chan->getSessions($this->storage) as $user) {
                        $user->sendCMD(CMD::CMD_TOPIC, [$cmd->getArg(0)], $cmd->getArg(1));
                    }
                }
            }
        }
    }

    public function handleINVITE(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 2) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $user = $this->storage->sessions()->searchByName($cmd->getArg(0));
            $chan = $this->storage->channels()->searchByName($cmd->getArg(1));
            if (null === $user) {
                $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
            } elseif (null === $chan || !$chan->hasSession($sess)) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
            } elseif ($chan->hasSession($sess)) {
                $sess->sendERR(ERR::ERR_USER_ON_CHANNEL, [$chan->getName()]);
            } elseif ($chan->hasFlag(ChannelInterface::FLAG_INVITE_ONLY) && !$chan->hasOperator($sess)) {
                $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
            } else {
                $chan->addInvited($user);
                $user->sendCMD(CMD::CMD_INVITE, [$user->getNickname()], $chan->getName(), $sess->getPrefix());
                $sess->sendRPL(RPL::RPL_INVITING, [$chan->getName(), $user->getNickname()]);
                if ($user->hasFlag(SessionInterface::FLAG_AWAY)) {
                    $sess->sendRPL(RPL::RPL_AWAY, [$user->getNickname()], $user->getAwayMessage());
                }
            }
        }
    }

    public function handleKICK(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 2) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $chan = $this->storage->channels()->searchByName($cmd->getArg(0));
            if (null === $chan) {
                $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$cmd->getArg(0)]);
            } elseif (!$chan->hasSession($sess)) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
            } elseif (!$chan->hasOperator($sess)) {
                $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
            } else {
                $user = $this->storage->sessions()->searchByName($cmd->getArg(1));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(1)]);
                } elseif (!$chan->hasSession($user)) {
                    $sess->sendERR(ERR::ERR_USER_NOT_IN_CHANNEL, [$cmd->getArg(1), $cmd->getArg(0)]);
                } else {
                    foreach ($chan->getSessions($this->storage) as $s) {
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
                }
            }
        }
    }

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