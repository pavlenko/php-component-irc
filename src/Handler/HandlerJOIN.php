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
            return $sess->sendERR(ERR::NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        $channels = array_filter(explode(',', (string) $cmd->getArg(0)));
        $keys     = array_filter(explode(',', (string) $cmd->getArg(1)));

        foreach ($channels as $index => $name) {
            $key = $keys[$index] ?? null;

            if (!$stor->isValidChannelName($name)) {
                $sess->sendERR(ERR::NO_SUCH_CHANNEL, [$name]);
            } elseif ($stor->conf(Config::CFG_MAX_CHANNELS) > 0 && $stor->conf(Config::CFG_MAX_CHANNELS) <= $sess->numChannels()) {
                $sess->sendERR(ERR::TOO_MANY_CHANNELS, [$name]);
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
                    $sess->sendERR(ERR::TOO_MANY_CHANNELS, [$name]);
                    continue;
                }

                if ($chan->getLimit() > 0 && $chan->numSessions() >= $chan->getLimit()) {
                    $sess->sendERR(ERR::CHANNEL_IS_FULL, [$name]);
                    continue;
                }

                if ($chan->hasFlag(Channel::FLAG_INVITE_ONLY) && !$chan->hasInvited($sess)) {
                    $sess->sendERR(ERR::INVITE_ONLY_CHANNEL, [$name]);
                    continue;
                }

                foreach ($chan->getBanMasks() as $mask) {
                    if ($stor->isEqualToRegex($mask, $sess->getPrefix())) {
                        $sess->sendERR(ERR::BANNED_FROM_CHANNEL, [$name]);
                        continue 2;
                    }
                }

                if ($chan->hasSession($sess)) {
                    continue;
                }

                $chan->delInvited($sess);
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
            $user->sendCMD(CMD::JOIN, [], $chan->getName(), $sess->getPrefix());
        }

        $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::TOPIC, [$chan->getName()]), $sess);
        $stor->trigger(Connection::EVT_INPUT, new CMD(CMD::NAMES, [$chan->getName()]), $sess);
    }
}