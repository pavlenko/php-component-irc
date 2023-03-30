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

    public function handlePRIVMSG(CMD $cmd, Connection $conn): void
    {}

    public function handleNOTICE(CMD $cmd, Connection $conn): void
    {}

    public function handleAWAY(CMD $cmd, Connection $conn): void
    {}

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
                            (!$channel->hasFlag(Channel::FLAG_SECRET) && !$channel->hasFlag(Channel::FLAG_PRIVATE)) ||
                            $channel->sessions()->containsName($sess->getNickname())
                        ) {
                            $channelName = $channel->getName();
                            //TODO
                            /*if (userChannels[j]->isOperator(*(connectedUsers[i])))
                                $userStatus = "@";
                            else if (userChannels[j]->isSpeaker(*(connectedUsers[i])))
                                $userStatus = "+";*/
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

    public function handleWHOIS(CMD $cmd, Connection $conn): void
    {}

    public function handleWHOWAS(CMD $cmd, Connection $conn): void
    {}
}