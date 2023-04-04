<?php

namespace PE\Component\IRC;

trait HandleOperatorCommands
{
    public function handleKILL(CMD $cmd, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } elseif ($cmd->numArgs() === 0 || empty($cmd->getComment())) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif ($this->config(Config::CFG_SERVER_NAME) === $cmd->getArg(0)) {
            $sess->sendERR(ERR::ERR_CANNOT_KILL_SERVER);
        } else {
            $user = $this->sessions->searchByName($cmd->getArg(0));
            if (null === $user) {
                $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(0)]);
            } else {
                //TODO check what response needed
                //$user->sendCMD('', [], $cmd->getComment());
                $user->sendCMD($cmd->getCode(), [$user->getNickname()], $cmd->getComment(), $user->getPrefix());
                $user->close();
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
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } else {
            foreach ($this->sessions as $user) {
                $user->close();
            }
            $this->stop();
            $this->config->load();
            $this->listen();
            $this->loop->run();
        }
    }
}