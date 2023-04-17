<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * LINKS [[<remote_server>] <server_mask>]
 * </code>
 */
class HandlerLINKS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        $remote  = $cmd->numArgs() > 1 ? $cmd->getArg(0) : null;
        $pattern = $cmd->numArgs() > 1 ? $cmd->getArg(1) : $cmd->getArg(0);

        if ($remote) {
            /** @var $serv SessionInterface */
            foreach ($stor->servers() as $serv) {/*TODO discriminate sessions by type: server/client*/
                if ($stor->isEqualToRegex($pattern, $serv->getServername())) {
                    return $serv->sendCMD(CMD::CMD_LINKS);
                }
            }
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$remote]);
        }

        /** @var $serv SessionInterface */
        foreach ($stor->servers() as $serv) {
            if (empty($pattern) || $stor->isEqualToRegex($pattern, $serv->getServername())) {
                $sess->sendRPL(
                    RPL::RPL_LINKS,
                    [$pattern, $serv->getServername()],
                    $serv->get('hop_count') . ' ' . $serv->get('info')/* hop count from serv auth, info from CMD(INFO)*/
                );
            }
        }

        return $sess->sendRPL(RPL::RPL_END_OF_LINKS, [$pattern]);
    }
}
