<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * LUSERS [<mask> [<target>]]
 * </code>
 */
class HandlerLUSERS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        $pattern = $cmd->getArg(0);
        $target  = $cmd->getArg(1);

        if ($target) {
            foreach ($stor->sessions(1) as $serv) {
                if ($stor->isEqualToRegex($target, $serv->getServername())) {
                    return $serv->sendCMD(CMD::CMD_LIST_USERS, $pattern);
                }
            }
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname(), $target]);
        }

        $normal    = 0;
        $invisible = 0;
        $operators = 0;
        $unknown   = 0;
        foreach ($stor->sessions() as $user) {
            if (!$user->hasFlag(SessionInterface::FLAG_REGISTERED)) {
                $unknown++;
                continue;
            }
            if ($user->hasFlag(SessionInterface::FLAG_IRC_OPERATOR)) {
                $operators++;
            }
            if ($user->hasFlag(SessionInterface::FLAG_INVISIBLE)) {
                $invisible++;
            } else {
                $normal++;
            }
        }

        $sess->sendRPL(
            RPL::RPL_L_USER_CLIENT,
            [$sess->getNickname()],
            sprintf('There are %d users and %d invisible on %d servers', $normal, $invisible, count($stor->sessions(1)))
        );

        $sess->sendRPL(RPL::RPL_L_USER_OPERATORS, [$operators]);
        $sess->sendRPL(RPL::RPL_L_USER_UNKNOWN, [$unknown]);
        $sess->sendRPL(RPL::RPL_L_USER_CHANNELS, [count($stor->channels())]);

        return $sess->sendRPL(
            RPL::RPL_L_USER_ME,
            [],
            sprintf('I have %d clients and %d servers', count($stor->sessions()), count($stor->sessions(1)))
        );
    }
}
