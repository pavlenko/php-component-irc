<?php

namespace PE\Component\IRC;

trait HandleUserCommands
{
    private function isEqualToRegex(string $pattern, string $subject): bool
    {
        $parts = preg_split('/\*+/', $pattern);
        $parts = array_map(fn($p) => preg_quote($p), $parts);

        $compiled = '/^' . implode('[\w\-\[\]\\\`^{}]+', $parts) . '$/';

        return preg_match($compiled, $subject);
    }

    public function handlePRIVMSG(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NO_RECIPIENT, [$cmd->getCode()]);
        } elseif (empty($cmd->getComment())) {
            $sess->sendERR(ERR::ERR_NO_TEXT_TO_SEND);
        } else {
            $receivers = array_filter(explode(',', $cmd->getArg(0)));
            if (
                $cmd->getCode() === CMD::CMD_NOTICE &&
                (count($receivers) > 1 || in_array($cmd->getArg(0)[0], ['#', '&']))
            ) {
                $sess->sendERR(ERR::ERR_NO_SUCH_NICK, $cmd->getArg(0));
                return;
            }
            $unique = [];
            foreach ($receivers as $receiver) {
                if (in_array($receiver, $unique)) {
                    $sess->sendERR(ERR::ERR_TOO_MANY_TARGETS, $receiver);
                    return;
                }
                if (in_array($receiver[0], ['#', '&'])) {
                    $chan = $this->storage->channels()->searchByName($receiver);
                    if (null === $chan) {
                        $sess->sendERR(ERR::ERR_NO_SUCH_NICK, $receiver);
                        return;
                    }
                    if (!$chan->hasSession($sess)) {
                        $sess->sendERR(ERR::ERR_CANNOT_SEND_TO_CHANNEL, $receiver);
                        return;
                    }
                    if (
                        $chan->hasFlag(ChannelInterface::FLAG_MODERATED) &&
                        !$chan->hasOperator($sess) &&
                        !$chan->hasSpeaker($sess)
                    ) {
                        $sess->sendERR(ERR::ERR_CANNOT_SEND_TO_CHANNEL, $receiver);
                        continue;
                    }
                } elseif (!$this->storage->sessions()->searchByName($receiver)) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, $receiver);
                    return;
                }
                $unique[] = $receiver;
            }
            foreach ($unique as $receiver) {
                if (in_array($receiver[0], ['#', '&'])) {
                    $chan = $this->storage->channels()->searchByName($receiver);
                    foreach ($chan->getSessions($this->storage) as $user) {
                        if ($user === $sess) {
                            continue;
                        }
                        $user->sendCMD($cmd->getCode(), [$receiver], $cmd->getComment(), $sess->getPrefix());
                    }
                } else {
                    $user = $this->storage->sessions()->searchByName($receiver);
                    if ($cmd->getCode() === CMD::CMD_PRIVATE_MSG && $user->hasFlag(SessionInterface::FLAG_AWAY)) {
                        $sess->sendRPL(RPL::RPL_AWAY, [$user->getNickname()], $user->getAwayMessage());
                    } elseif ($cmd->getCode() !== CMD::CMD_NOTICE || $user->hasFlag(SessionInterface::FLAG_RECEIVE_NOTICE)) {
                        $user->sendCMD($cmd->getCode(), [$receiver], $cmd->getComment(), $sess->getPrefix());
                    }
                }
            }
        }
    }

    public function handleNOTICE(CMD $cmd, SessionInterface $sess): void
    {
        $this->handlePRIVMSG($cmd, $sess);
    }

    public function handleWHO(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            foreach ($this->storage->sessions() as $user) {
                if (
                    $this->isEqualToRegex($cmd->getArg(0), $user->getNickname()) &&
                    !$user->hasFlag(SessionInterface::FLAG_INVISIBLE)
                ) {
                    $channelName = '*';
                    $userStatus  = '';

                    foreach ($user->getChannels($this->storage) as $channel) {
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
                        ($cmd->getArg(1) === 'o' && $user->hasFlag(SessionInterface::FLAG_IS_OPERATOR))
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
            $sess->sendRPL(RPL::RPL_END_OF_WHO);
        }
    }
}
