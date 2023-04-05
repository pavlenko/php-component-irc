<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\Channel;
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
            } elseif ($stor->conf(Config::CFG_MAX_CHANNELS) > 0 && $stor->conf(Config::CFG_MAX_CHANNELS) <= count($sess->channels())) {
                $sess->sendERR(ERR::ERR_TOO_MANY_CHANNELS, [$name]);
            } else {
                $chan = $stor->channels()->searchByName($name);
                //TODO debug this
                if (null === $chan) {
                    $chan = new Channel($name, $key);
                    $chan->operators()->attach($sess);
                    $stor->channels()->attach($chan);
                } elseif ($chan->hasFlag(Channel::FLAG_PRIVATE) && $key !== $chan->getPass()) {
                    $sess->sendERR(ERR::ERR_TOO_MANY_CHANNELS, [$name]);
                    continue;
                } elseif ($chan->getLimit() > 0 && count($chan->sessions()) >= $chan->getLimit()) {
                    $sess->sendERR(ERR::ERR_CHANNEL_IS_FULL, [$name]);
                    continue;
                } elseif ($chan->hasFlag(Channel::FLAG_INVITE_ONLY) && $chan->invited()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_INVITE_ONLY_CHANNEL, [$name]);
                    continue;
                } else {
                    foreach ($chan->getBanMasks() as $mask) {
                        if ($stor->isEqualToRegex($mask, $sess->getPrefix())) {
                            $sess->sendERR(ERR::ERR_BANNED_FROM_CHANNEL, [$name]);
                            continue 2;
                        }
                    }

                    if ($chan->sessions()->searchByName($sess->getNickname())) {
                        continue;
                    }

                    $chan->invited()->detach($sess);
                }

                $chan->sessions()->attach($sess);
                $sess->channels()->attach($chan);

                foreach ($chan->sessions() as $user) {
                    $user->sendCMD($cmd->getCode(), [], $chan->getName(), $sess->getPrefix());
                }

                $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::CMD_TOPIC, [$chan->getName()]), $sess);
                $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::CMD_NAMES, [$chan->getName()]), $sess);
            }
        }
        return 0;
    }
}