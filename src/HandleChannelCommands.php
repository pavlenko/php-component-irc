<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    private function handleChannelFlags(CMD $cmd, SessionInterface $sess, ChannelInterface $chan)
    {
        $flag = $cmd->getArg(1);
        if ('o' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                $user = $this->sessions->searchByName($cmd->getArg(2));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
                } elseif ('+' === $flag[0]) {
                    $chan->operators()->attach($user);
                } elseif ('-' === $flag[0]) {
                    $chan->operators()->detach($user);
                }
            }
        } elseif ('p' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_PRIVATE);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_PRIVATE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_SECRET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_SECRET);
            }
        } elseif ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_INVITE_ONLY);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_INVITE_ONLY);
            }
        } elseif ('t' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_TOPIC_SET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_TOPIC_SET);
            }
        } elseif ('m' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_MODERATED);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_MODERATED);
            }
        } elseif ('l' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setLimit((int) $cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setLimit(0);
                }
            }
        } elseif ('k' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setPass($cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setPass('');
                }
            }
        } elseif ('b' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->addBanMask($cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->delBanMask($cmd->getArg(2));
                }
            }
        } elseif ('v' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                $user = $this->sessions->searchByName($cmd->getArg(2));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
                } elseif ('+' === $flag[0]) {
                    $chan->speakers()->attach($user);
                } elseif ('-' === $flag[0]) {
                    $chan->speakers()->detach($user);
                }
            }
        } elseif ('n' !== $flag[1]) {
            $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
    }

    private function handleSessionFlags(CMD $cmd, SessionInterface $sess)
    {
        $flag = $cmd->getArg(1);
        if ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_INVISIBLE);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_INVISIBLE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_RECEIVE_NOTICE);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_RECEIVE_NOTICE);
            }
        } elseif ('w' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_RECEIVE_WALLOPS);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_RECEIVE_WALLOPS);
            }
        } elseif ('-o') {
            $sess->clrFlag(SessionInterface::FLAG_IS_OPERATOR);
        } else {
            $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
    }

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
            $channels = explode(',', $cmd->getArg(0));
            $keys     = explode(',', $cmd->getArg(1));

            //TODO loop through channels
            //TODO get key for each channel
            //TODO check channel name valid
            //TODO che user max channels
            //TODO connect to channel...???
        }
    }

    public function handleTOPIC(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleINVITE(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleKICK(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handlePART(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleNAMES(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleLIST(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}
}