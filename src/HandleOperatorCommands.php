<?php

namespace PE\Component\IRC;

trait HandleOperatorCommands
{
    public function handleKILL(CMD $cmd, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } elseif ($cmd->numArgs() < 2) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif ($this->config(Config2::CFG_SERVERNAME) === $cmd->getArg(0)) {
            $sess->sendERR(ERR::ERR_CANNOT_KILL_SERVER);
        } else {
            $user = $this->sessions->searchByName($cmd->getArg(0));
            if (null === $user) {
                $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
            } else {
                $sess->sendCMD($cmd->getArg(1));//TODO check
                $sess->close();
            }
        }
    }

    public function handleREHASH(CMD $cmd, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } else {
            $this->config->load();
            $sess->sendRPL(RPL::RPL_REHASHING, [$this->config->path()]);
        }
    }

    public function handleRESTART(CMD $cmd, SessionInterface $sess): void
    {
        //TODO close all user sessions
        //TODO reload config
        //TODO recreate server socket and start listen to it
    }
}