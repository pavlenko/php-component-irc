<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\ChannelInterface;
use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerPRIVMSG implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::NO_RECIPIENT, [$cmd->getCode()]);
        }

        if (empty($cmd->getComment())) {
            return $sess->sendERR(ERR::NO_TEXT_TO_SEND);
        }

        $targets = array_filter(explode(',', $cmd->getArg(0)));
        if ($cmd->getCode() === CMD::NOTICE && (count($targets) > 1 || in_array($cmd->getArg(0)[0], ['#', '&']))) {
            return $sess->sendERR(ERR::NO_SUCH_NICK, $cmd->getArg(0));
        }

        $unique = [];
        foreach ($targets as $receiver) {
            if (in_array($receiver, $unique)) {
                return $sess->sendERR(ERR::TOO_MANY_TARGETS, $receiver);
            }

            if (in_array($receiver[0], ['#', '&'])) {
                $chan = $stor->channels()->searchByName($receiver);
                if (null === $chan) {
                    return $sess->sendERR(ERR::NO_SUCH_NICK, $receiver);
                }

                if (!$chan->hasSession($sess)) {
                    return $sess->sendERR(ERR::CANNOT_SEND_TO_CHANNEL, $receiver);
                }

                $canSend = !$chan->hasFlag(ChannelInterface::FLAG_MODERATED)
                    || $chan->hasOperator($sess)
                    || $chan->hasSpeaker($sess);

                if (!$canSend) {
                    $sess->sendERR(ERR::CANNOT_SEND_TO_CHANNEL, $receiver);
                    continue;
                }
            } elseif (!$stor->sessions()->searchByName($receiver)) {
                return $sess->sendERR(ERR::NO_SUCH_NICK, $receiver);
            }
            $unique[] = $receiver;
        }

        foreach ($unique as $receiver) {
            if (in_array($receiver[0], ['#', '&'])) {
                $chan = $stor->channels()->searchByName($receiver);
                foreach ($chan->getSessions($stor) as $user) {
                    if ($user === $sess) {
                        continue;
                    }
                    $user->sendCMD($cmd->getCode(), [$receiver], $cmd->getComment(), $sess->getPrefix());
                }
            } else {
                $user = $stor->sessions()->searchByName($receiver);

                if ($cmd->getCode() === CMD::PRIVATE_MSG && $user->hasFlag(SessionInterface::FLAG_AWAY)) {
                    $sess->sendRPL(RPL::AWAY, [$user->getNickname()], $user->getAwayMessage());
                    continue;
                }

                if ($cmd->getCode() !== CMD::NOTICE || $user->hasFlag(SessionInterface::FLAG_RECEIVE_NOTICE)) {
                    $user->sendCMD($cmd->getCode(), [$receiver], $cmd->getComment(), $sess->getPrefix());
                }
            }
        }
        return 0;
    }
}
