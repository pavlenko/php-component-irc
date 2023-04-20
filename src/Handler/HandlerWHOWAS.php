<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

final class HandlerWHOWAS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::NO_NICKNAME_GIVEN);
        }

        $user = $stor->sessions()->searchByName($cmd->getArg(0));
        if (null === $user) {
            $history = $stor->history()->getByName($cmd->getArg(0));
            if (empty($history)) {
                return $sess->sendERR(ERR::WAS_NO_SUCH_NICK, [$cmd->getArg(0)]);
            }

            $limit = $cmd->getArg(1) ?: PHP_INT_MAX;
            for ($i = 0; $i < count($history) && $i < $limit; $i++) {
                $sess->sendRPL(RPL::WHO_WAS_USER, [
                    $history[$i]->getNickname(),
                    $history[$i]->getUsername(),
                    $history[$i]->getHostname(),
                    '*',
                ], $history[$i]->getRealname());

                $sess->sendRPL(
                    RPL::WHO_IS_SERVER,
                    [$history[$i]->getNickname(), $history[$i]->getServername()],
                    $stor->conf(Config::CFG_INFO)
                );
            }
        }
        return $sess->sendRPL(RPL::END_OF_WHO_WAS, [$cmd->getArg(0)]);
    }
}
