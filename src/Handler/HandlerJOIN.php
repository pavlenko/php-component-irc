<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\Channel;
use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\Connection;
use PE\Component\IRC\ERR;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerJOIN implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $channels = array_filter(explode(',', (string) $cmd->getArg(0)));
        $keys     = array_filter(explode(',', (string) $cmd->getArg(1)));

        foreach ($channels as $index => $name) {
            $key = $keys[$index] ?? null;

            if (!$stor->isValidChannelName($name)) {
                $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$name]);
            } elseif ($stor->conf(Config::CFG_MAX_CHANNELS) > 0 && $stor->conf(Config::CFG_MAX_CHANNELS) <= $sess->numChannels()) {
                $sess->sendERR(ERR::ERR_TOO_MANY_CHANNELS, [$name]);
            } else {
                $chan = $stor->channels()->searchByName($name);

                if (null === $chan) {
                    $chan = new Channel($name, $key);

                    $stor->channels()->attach($chan);
                    $sess->addChannel($chan);

                    $chan->addSession($sess);
                    $chan->addOperator($sess);

                    $this->sendInfo($chan, $sess, $stor);
                    continue;
                }

                if ($chan->hasFlag(Channel::FLAG_PRIVATE) && $key !== $chan->getPass()) {
                    $sess->sendERR(ERR::ERR_TOO_MANY_CHANNELS, [$name]);
                    continue;
                }

                if ($chan->getLimit() > 0 && $chan->numSessions() >= $chan->getLimit()) {
                    $sess->sendERR(ERR::ERR_CHANNEL_IS_FULL, [$name]);
                    continue;
                }

                if ($chan->hasFlag(Channel::FLAG_INVITE_ONLY) && !$chan->invited()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_INVITE_ONLY_CHANNEL, [$name]);
                    continue;
                }

                foreach ($chan->getBanMasks() as $mask) {
                    if ($stor->isEqualToRegex($mask, $sess->getPrefix())) {
                        $sess->sendERR(ERR::ERR_BANNED_FROM_CHANNEL, [$name]);
                        continue 2;
                    }
                }

                if ($chan->hasSession($sess)) {
                    continue;
                }

                $chan->invited()->detach($sess);

                $chan->addSession($sess);
                $sess->addChannel($chan);

                $this->sendInfo($chan, $sess, $stor);
            }
        }
        return 0;
    }

    private function sendInfo(ChannelInterface $chan, SessionInterface $sess, StorageInterface $stor): void
    {
        foreach ($chan->getSessions($stor) as $user) {
            $user->sendCMD(CMD::CMD_JOIN, [], $chan->getName(), $sess->getPrefix());
        }

        $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::CMD_TOPIC, [$chan->getName()]), $sess);
        $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::CMD_NAMES, [$chan->getName()]), $sess);
    }
}