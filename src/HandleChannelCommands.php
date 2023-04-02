<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    public function handleMODE(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $name = $cmd->getArg(0);
            $flag = $cmd->getArg(1);
            if ('#' === $name[0]) {
                $chan = $this->channels->searchByName($name);
                if (null === $chan) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$name]);
                } elseif (!$chan->operators()->searchByName($sess->getNickname())) {
                    //TODO allow read flags
                    $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$name]);
                } elseif (!$chan->sessions()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$name]);
                } elseif ($cmd->numArgs() === 1) {
                    $sess->sendRPL(RPL::RPL_CHANNEL_MODE_IS, [$name, $chan->getFlagsAsString()]);
                } else {
                    $this->handleChannelFlags($cmd, $sess, $chan);
                    foreach ($chan->sessions() as $user) {
                        $user->sendCMD(CMD::CMD_MODE, [
                            $name,
                            $flag,
                            ('o' === $flag[1] || 'v' === $flag[1]) ? $cmd->getArg(2) : ''
                        ], null, $sess->getPrefix());
                    }
                }
            } else {
                if ($cmd->getArg(0) !== $sess->getNickname()) {
                    $sess->sendERR(ERR::ERR_USERS_DONT_MATCH, [$name]);
                } elseif ($cmd->numArgs() === 1) {
                    $sess->sendRPL(RPL::RPL_USER_MODE_IS, [$sess->getFlagsAsString()]);
                } else {
                    $this->handleSessionFlags($cmd, $sess);
                    $sess->sendCMD(CMD::CMD_MODE, [$name, $flag], null, $sess->getPrefix());
                }
            }
        }
    }

    public function handleJOIN(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $channels = array_filter(explode(',', (string) $cmd->getArg(0)));
            $keys     = array_filter(explode(',', (string) $cmd->getArg(1)));

            foreach ($channels as $index => $name) {
                $key = $keys[$index] ?? null;

                if (!$this->isValidChannelName($name)) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$name]);
                } elseif ($this->config->getMaxChannels() > 0 && $this->config->getMaxChannels() <= count($sess->channels())) {
                    $sess->sendERR(ERR::ERR_TOO_MANY_CHANNELS, [$name]);
                } else {
                    $chan = $this->channels->searchByName($name);
                    if (null === $chan) {
                        $this->channels->attach($chan = new Channel($sess, $name, $key));
                    } else {
                        $chan->sessions()->attach($sess);
                    }
                    $sess->channels()->attach($chan);
                    foreach ($chan->sessions() as $s) {
                        $s->sendCMD($cmd->getCode(), [], $chan->getName(), $sess->getPrefix());
                    }
                    $this->handleTOPIC(new CMD(CMD::CMD_TOPIC, [$chan->getName()]), $sess);
                    $this->handleNAMES(new CMD(CMD::CMD_NAMES, [$chan->getName()]), $sess);
                }
            }
        }
    }

    public function handleTOPIC(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $chan = $this->channels->searchByName($cmd->getArg(0));
            if (null === $chan || !$chan->sessions()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$cmd->getCode()]);
            } elseif ($cmd->numArgs() < 2) {
                if (!empty($chan->getTopic())) {
                    $sess->sendRPL(RPL::RPL_TOPIC, [$cmd->getArg(0)], $chan->getTopic());
                } else {
                    $sess->sendRPL(RPL::RPL_NO_TOPIC, [$cmd->getArg(0)]);
                }
            } else {
                if (
                    $chan->hasFlag(ChannelInterface::FLAG_TOPIC_SET) &&
                    !$chan->operators()->searchByName($sess->getNickname())
                ) {
                    $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$cmd->getArg(0)]);
                } else {
                    $chan->setTopic($cmd->getArg(1));
                    foreach ($chan->sessions() as $user) {
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
            $user = $this->sessions->searchByName($cmd->getArg(0));
            $chan = $this->channels->searchByName($cmd->getArg(1));
            if (null === $user) {
                $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
            } elseif (null === $chan || null === $chan->sessions()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
            } elseif (null !== $chan->sessions()->searchByName($user->getNickname())) {
                $sess->sendERR(ERR::ERR_USER_ON_CHANNEL, [$chan->getName()]);
            } elseif ($chan->hasFlag(ChannelInterface::FLAG_INVITE_ONLY) && !$chan->operators()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
            } else {
                $chan->invited()->attach($user);
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
            $chan = $this->channels->searchByName($cmd->getArg(0));
            if (null === $chan) {
                $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$cmd->getArg(0)]);
            } elseif (!$chan->operators()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$chan->getName()]);
            } elseif (!$chan->operators()->searchByName($sess->getNickname())) {
                $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
            } else {
                $user = $this->sessions->searchByName($cmd->getArg(1));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(1)]);
                } elseif (!$chan->sessions()->searchByName($user->getNickname())) {
                    $sess->sendERR(ERR::ERR_USER_NOT_IN_CHANNEL, [$cmd->getArg(1), $cmd->getArg(0)]);
                } else {
                    foreach ($chan->sessions() as $s) {
                        $s->sendCMD(
                            $cmd->getCode(),
                            [$chan->getName(), $user->getNickname()],
                            $cmd->numArgs() > 2 ? $cmd->getArg(2) : $sess->getNickname()
                        );
                    }
                    $chan->sessions()->detach($user);
                    $chan->speakers()->detach($user);
                    $chan->operators()->detach($user);
                    $user->channels()->detach($chan);
                }
            }
        }
    }

    public function handlePART(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 2) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $channels = explode(',', $cmd->getArg(0));
            foreach ($channels as $channel) {
                $chan = $this->channels->searchByName($channel);
                if (null === $chan) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$channel]);
                } elseif (!$chan->sessions()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$chan->getName()]);
                } else {
                    foreach ($chan->sessions() as $user) {
                        $user->sendCMD($cmd->getCode(), [$chan->getName()]);
                    }
                    $chan->sessions()->detach($sess);
                    $chan->speakers()->detach($sess);
                    $chan->operators()->detach($sess);
                    $sess->channels()->detach($chan);
                }
            }
        }
    }

    public function handleNAMES(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            foreach ($this->channels as $chan) {
                if (
                    !$chan->hasFlag(ChannelInterface::FLAG_PRIVATE) &&
                    !$chan->hasFlag(ChannelInterface::FLAG_SECRET)
                ) {
                    $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['= ' . $chan->getName()], $chan->getNamesAsString());// '= <name>' ???
                }
            }
            $names = [];
            foreach ($this->sessions as $user) {
                if (!count($user->channels())) {
                    $names[] = $user->getNickname();
                }
            }
            $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['* *'], implode(' ', $names));
            $sess->sendRPL(RPL::RPL_END_OF_NAMES, ['*']);
        } else {
            $channels = array_filter(explode(',', $cmd->getArg(0)));
            foreach ($channels as $name) {
                $chan = $this->channels->searchByName($name);
                if (
                    null !== $chan &&
                    !$chan->hasFlag(ChannelInterface::FLAG_PRIVATE) &&
                    !$chan->hasFlag(ChannelInterface::FLAG_SECRET)
                ) {
                    $sess->sendRPL(RPL::RPL_NAMES_REPLY, ['= ' . $chan->getName()], $chan->getNamesAsString());// '= <name>' ???
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
            foreach ($this->channels as $chan) {
                if (!empty($names) && !in_array($chan->getName(), $names)) {
                    continue;
                }
                if ($chan->hasFlag(ChannelInterface::FLAG_SECRET) && !$chan->sessions()->searchByName($sess->getNickname())) {
                    continue;
                }
                if ($chan->hasFlag(ChannelInterface::FLAG_PRIVATE) && !$chan->sessions()->searchByName($sess->getNickname())) {
                    $name = '*';
                } else {
                    $name  = $chan->getName();
                    $info  = '[' . $chan->getFlagsAsString() . '] ' . $chan->getTopic();
                }
                //TODO filter users by visible flag???
                $sess->sendRPL(RPL::RPL_LIST, [$name, count($chan->sessions())], $info ?? null);
            }
            $sess->sendRPL(RPL::RPL_LIST_END);
        }
    }
}