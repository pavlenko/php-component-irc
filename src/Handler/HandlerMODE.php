<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerMODE implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $name = $cmd->getArg(0);
        $flag = $cmd->getArg(1);

        if ('#' === $name[0]) {
            $chan = $stor->channels()->searchByName($name);
            if (null === $chan) {
                return $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$name]);
            }

            if ($cmd->numArgs() === 1) {
                return $sess->sendRPL(RPL::RPL_CHANNEL_MODE_IS, [$name, $chan->getFlagsAsString()]);
            }

            if ('+b' === $flag && $cmd->numArgs() === 2) {
                foreach ($chan->getBanMasks() as $mask) {
                    $sess->sendRPL(RPL::RPL_BAN_LIST, [$cmd->getArg(0), $mask]);
                }
                return $sess->sendRPL(RPL::RPL_END_OF_BAN_LIST, [$cmd->getArg(0)]);
            }

            if (!$chan->hasOperator($sess)) {
                return $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$name]);
            }

            if (!$chan->hasSession($sess)) {
                return $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$name]);
            }

            if ($cmd->numArgs() < 3 && preg_match('/^\+[bklov]|-[bov]$/', $flag)) {
                return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            }

            if ($this->handleChannelFlags($cmd, $sess, $stor) === -1) {
                foreach ($chan->getSessions($stor) as $user) {
                    $resp = $flag . ' ' . (in_array($flag[1], ['o', 'v']) ? $cmd->getArg(2) : '');
                    $user->sendCMD(CMD::CMD_MODE, [$name, trim($resp)], null, $sess->getServername());
                }
            }
        } else {
            if ($cmd->getArg(0) !== $sess->getNickname()) {
                return $sess->sendERR(ERR::ERR_USERS_DONT_MATCH, [$name]);
            }

            if ($cmd->numArgs() === 1) {
                return $sess->sendRPL(RPL::RPL_USER_MODE_IS, [$sess->getFlagsAsString()]);
            }

            if ($this->handleSessionFlags($cmd, $sess) === -1) {
                $sess->sendCMD(CMD::CMD_MODE, [$name], $flag, $sess->getPrefix());
            }
        }
        return 0;
    }

    private function handleChannelFlags(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        $chan = $stor->channels()->searchByName($cmd->getArg(0));
        $flag = $cmd->getArg(1);

        if ('o' === $flag[1]) {
            $user = $stor->sessions()->searchByName($cmd->getArg(2));
            if (null === $user) {
                return $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
            }
            if ('+' === $flag[0]) {
                $chan->addOperator($user);
            }
            if ('-' === $flag[0]) {
                $chan->delOperator($user);
            }
        } elseif ('p' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_PRIVATE);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_PRIVATE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_SECRET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_SECRET);
            }
        } elseif ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_INVITE_ONLY);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_INVITE_ONLY);
            }
        } elseif ('t' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_TOPIC_SET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_TOPIC_SET);
            }
        } elseif ('m' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_MODERATED);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_MODERATED);
            }
        } elseif ('l' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setLimit((int) $cmd->getArg(2));
            }
            if ('-' === $flag[0]) {
                $chan->setLimit(0);
            }
        } elseif ('k' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setPass($cmd->getArg(2));
            }
            if ('-' === $flag[0]) {
                $chan->setPass('');
            }
        } elseif ('b' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->addBanMask($cmd->getArg(2));
            }
            if ('-' === $flag[0]) {
                $chan->delBanMask($cmd->getArg(2));
            }
        } elseif ('v' === $flag[1]) {
            $user = $stor->sessions()->searchByName($cmd->getArg(2));
            if (null === $user) {
                return $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
            }
            if ('+' === $flag[0]) {
                $chan->addSpeaker($user);
            }
            if ('-' === $flag[0]) {
                $chan->delSpeaker($user);
            }
        } elseif ('n' !== $flag[1]) {
            return $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
        return -1;
    }

    private function handleSessionFlags(CMD $cmd, SessionInterface $sess): int
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
            $sess->clrFlag(SessionInterface::FLAG_IRC_OPERATOR);
        } else {
            return $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
        return -1;
    }
}