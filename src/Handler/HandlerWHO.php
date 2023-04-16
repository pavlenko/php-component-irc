<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerWHO implements HandlerInterface
{
    //TODO :<prefix> <rpl> <channel> <user> <host> <server> <nick> <H|G>[*][@|+] :<hop count> <real name>
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        $sessions = [];
        if ($cmd->numArgs() === 0) {
            foreach ($stor->sessions() as $s) {
                if ($sess === $s) {
                    continue;
                }
                $hasCommonChannel = false;
                foreach ($sess->getChannels($stor) as $c) {
                    if ($s->hasChannel($c)) {
                        $hasCommonChannel = true;
                        break;
                    }
                }
                if ($hasCommonChannel) {
                    continue;
                }
                $sessions[] = $s;
            }
        } else {
            $chan = $stor->channels()->searchByName($cmd->getArg(0));
            if (null !== $chan) {
                foreach ($chan->getSessions($stor) as $user) {
                    $this->replyWHO($cmd, $sess, $chan, $user);
                }
                return $sess->sendRPL(RPL::RPL_END_OF_WHO, $cmd->getArg(0));
            } else {
                //TODO list channels & end of who per match sessions
                foreach ($stor->sessions() as $s) {
                    $equal = $stor->isEqualToRegex($cmd->getArg(0), $s->getHostname())
                        || $stor->isEqualToRegex($cmd->getArg(0), $s->getServername())
                        || $stor->isEqualToRegex($cmd->getArg(0), $s->getRealname())
                        ||$stor->isEqualToRegex($cmd->getArg(0), $s->getNickname());

                    if ($equal) {
                        $sessions[] = $s;
                    }
                }
                return 0;
            }
        }

        $sessions = array_filter($sessions, fn($s) => !$s->hasFlag(SessionInterface::FLAG_INVISIBLE));//

        //TODO end of who must follow each list item if arg0 passed, elsewhere without name at end of reply
return 0;
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        foreach ($stor->sessions() as $user) {
            if (
                $stor->isEqualToRegex($cmd->getArg(0), $user->getNickname()) &&
                !$user->hasFlag(SessionInterface::FLAG_INVISIBLE)
            ) {
                $channelName = '*';
                $userStatus  = '';

                foreach ($user->getChannels($stor) as $channel) {
                    if (
                        (!$channel->hasFlag(ChannelInterface::FLAG_SECRET) && !$channel->hasFlag(ChannelInterface::FLAG_PRIVATE)) ||
                        $channel->hasSession($sess)
                    ) {
                        $channelName = $channel->getName();
                        if ($channel->hasOperator($user)) {
                            $userStatus = '@';
                        } elseif ($channel->hasSpeaker($user)) {
                            $userStatus = '+';
                        }
                        break;
                    }
                }

                if (
                    $cmd->numArgs() === 1 ||
                    ($cmd->getArg(1) === 'o' && $user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR))
                ) {
                    $sess->sendRPL(RPL::RPL_WHO_REPLY, [
                        $channelName,
                        $user->getUsername(),
                        $user->getHostname(),
                        $user->getServername(),
                        $user->getNickname(),
                        'H' . $userStatus,
                    ], '0 ' . $user->getRealname());
                }
            }
        }
        return $sess->sendRPL(RPL::RPL_END_OF_WHO);
    }

    private function replyWHO(CMD $cmd, SessionInterface $sess, ChannelInterface $chan, SessionInterface $user): void
    {
        if ($cmd->getArg(1) === 'o' && !$user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
            return;
        }

        $userStatus = '';
        if ($chan->hasOperator($user)) {
            $userStatus = '@';
        } elseif ($chan->hasSpeaker($user)) {
            $userStatus = '+';
        }

        $sess->sendRPL(RPL::RPL_WHO_REPLY, [
            $chan->getName(),
            $user->getUsername(),
            $user->getHostname(),
            $user->getServername(),
            $user->getNickname(),
            'H' . $userStatus,
        ], '0 ' . $user->getRealname());
    }
}
