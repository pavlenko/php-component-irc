<?php

namespace PE\Component\IRC;

trait HandleOperatorCommands
{
    //TODO helpers
    public function handleKILL(CMD $cmd, SessionInterface $sess): void
    {}

    public function handleREHASH(CMD $cmd, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } else {
            $this->config->load();
            $sess->sendRPL(RPL::RPL_REHASHING);
        }
    }

    public function handleRESTART(CMD $cmd, SessionInterface $sess): void
    {}
}