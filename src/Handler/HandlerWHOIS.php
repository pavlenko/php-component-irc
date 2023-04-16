<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerWHOIS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NO_NICKNAME_GIVEN);
        }

        $suckNick = false;
        foreach ($stor->sessions() as $user) {
            $skip = !$stor->isEqualToRegex($cmd->getArg(0), $user->getNickname())
                || $user->hasFlag(SessionInterface::FLAG_IS_OPERATOR);
            if ($skip) {
                continue;
            }

            $sess->sendRPL(RPL::RPL_WHO_IS_USER, [
                $user->getNickname(),
                $user->getUsername(),
                $user->getHostname(),
                '*',
            ], $user->getRealname());

            $channels = [];
            foreach ($user->getChannels($stor) as $chan) {
                if ($chan->hasFlag(ChannelInterface::FLAG_SECRET) || $chan->hasFlag(ChannelInterface::FLAG_PRIVATE)) {
                    continue;
                }
                if ($chan->hasOperator($user)) {
                    $channels[] = '@' . $chan->getName();
                } elseif ($chan->hasSpeaker($user)) {
                    $channels[] = '+' . $chan->getName();
                } else {
                    $channels[] = $chan->getName();
                }
            }

            $sess->sendRPL(RPL::RPL_WHO_IS_CHANNELS, [$user->getNickname()], implode(' ', $channels));
            $sess->sendRPL(
                RPL::RPL_WHO_IS_SERVER,
                [$user->getNickname(), $user->getServername()],
                $stor->conf(Config::CFG_INFO)
            );

            if ($user->hasFlag(SessionInterface::FLAG_AWAY)) {
                $sess->sendRPL(RPL::RPL_AWAY, [$user->getNickname()], $user->getAwayMessage());
            }
            if ($user->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
                $sess->sendRPL(RPL::RPL_WHO_IS_OPERATOR, [$user->getNickname()]);
            }
            $sess->sendRPL(RPL::RPL_WHO_IS_IDLE, [
                $user->getNickname(),
                time() - $user->getRegistrationTime(),
                $user->getRegistrationTime(),
            ]);
            $suckNick = true;
        }
        if (!$suckNick) {
            $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
        }
        return $sess->sendRPL(RPL::RPL_END_OF_WHO_IS, [$cmd->getArg(0)]);
    }
}
