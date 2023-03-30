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
    {}

    public function handleNOTICE(CMD $cmd, SessionInterface $sess): void
    {}

    public function handleAWAY(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->clrFlag(SessionInterface::FLAG_AWAY);
            $sess->sendRPL(RPL::RPL_UN_AWAY);
        } else {
            $sess->setFlag(SessionInterface::FLAG_AWAY);
            $sess->setAwayMessage($cmd->getArg(0));
            $sess->sendRPL(RPL::RPL_NOW_AWAY);
        }
    }

    public function handleWHO(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            foreach ($this->sessions as $user) {
                if (
                    $this->isEqualToRegex($cmd->getArg(0), $user->getNickname()) &&
                    !$user->hasFlag(SessionInterface::FLAG_INVISIBLE)
                ) {
                    $channelName = '*';
                    $userStatus  = '';

                    foreach ($user->channels() as $channel) {
                        if (
                            (!$channel->hasFlag(ChannelInterface::FLAG_SECRET) && !$channel->hasFlag(ChannelInterface::FLAG_PRIVATE)) ||
                            $channel->sessions()->containsName($sess->getNickname())
                        ) {
                            $channelName = $channel->getName();
                            if ($channel->operators()->searchByName($user->getNickname())) {
                                $userStatus = '@';
                            } elseif ($channel->speakers()->searchByName($user->getNickname())) {
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

    public function handleWHOIS(CMD $cmd, SessionInterface $sess): void
    {}

    public function handleWHOWAS(CMD $cmd, SessionInterface $sess): void
    {}
}