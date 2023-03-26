<?php

namespace PE\Component\IRC;

trait HandleOtherCommands
{
    //TODO helpers
    public function handlePING(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if (!$cmd->numArgs()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_ORIGIN, [$sess->getNickname()]));
        } else {
            $conn->sendCMD(new CMD(CMD::CMD_PONG, [], $cmd->getArg(0), $this->config->getName()));
        }
    }

    public function handlePONG(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if (!$cmd->numArgs() || $cmd->getArg(0) !== $this->config->getName()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname()]));
        }
        $sess->clrFlag(SessionInterface::FLAG_PINGING);//TODO flag set in server ping timer
    }

    public function handleISON(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            $resp = [];
            foreach ($cmd->getArgs() as $arg) {
                if ($this->sessions->containsName($arg)) {
                    $resp[] = $arg;
                }
            }
            $conn->sendRPL(new RPL($this->config->getName(), RPL::RPL_IS_ON, [$sess->getNickname(), ...$resp]));
        }
    }

    public function handleINFO(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleTIME(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleADMIN(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleUSERHOST(CMD $cmd, Connection $conn): void
    {}

    public function handleVERSION(CMD $cmd, Connection $conn): void
    {}

    public function handleWALLOPS(CMD $cmd, Connection $conn): void
    {}
}