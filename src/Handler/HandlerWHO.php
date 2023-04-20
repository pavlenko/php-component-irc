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
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            foreach ($stor->sessions() as $user) {
                if ($sess === $user) {
                    continue;
                }
                $hasCommonChannel = false;
                foreach ($sess->getChannels($stor) as $chan) {
                    if ($user->hasChannel($chan)) {
                        $hasCommonChannel = true;
                        break;
                    }
                }
                if ($hasCommonChannel || $user->hasFlag(SessionInterface::FLAG_INVISIBLE)) {
                    continue;
                }
                foreach ($user->getChannels($stor) as $chan) {
                    $this->replyWHO($cmd, $sess, $chan, $user);
                }
            }
            return $sess->sendRPL(RPL::END_OF_WHO);
        }

        $chan = $stor->channels()->searchByName($cmd->getArg(0));
        if (null !== $chan) {
            foreach ($chan->getSessions($stor) as $user) {
                $this->replyWHO($cmd, $sess, $chan, $user);
            }
            return $sess->sendRPL(RPL::END_OF_WHO, [$cmd->getArg(0)]);
        }

        $suchNick = false;
        foreach ($stor->sessions() as $user) {
            $equal = $stor->isEqualToRegex($cmd->getArg(0), $user->getHostname())
                || $stor->isEqualToRegex($cmd->getArg(0), $user->getServername())
                || $stor->isEqualToRegex($cmd->getArg(0), $user->getRealname())
                || $stor->isEqualToRegex($cmd->getArg(0), $user->getNickname());

            if (!$equal) {
                continue;
            }
            $suchNick = true;
            foreach ($user->getChannels($stor) as $chan) {
                $this->replyWHO($cmd, $sess, $chan, $user);
            }
            $sess->sendRPL(RPL::END_OF_WHO, [$cmd->getArg(0)]);
        }
        if (!$suchNick) {
            $sess->sendERR(ERR::NO_SUCH_SERVER, [$cmd->getArg(0)]);
        }
        return 0;
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

        $sess->sendRPL(RPL::WHO_REPLY, [
            $chan->getName(),
            $user->getUsername(),
            $user->getHostname(),
            $user->getServername(),
            $user->getNickname(),
            'H' . $userStatus,
        ], '0 ' . $user->getRealname());
    }
}
