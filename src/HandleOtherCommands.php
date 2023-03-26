<?php

namespace PE\Component\IRC;

trait HandleOtherCommands
{
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
        if ($cmd->numArgs() === 0 || $cmd->getArg(0) !== $this->config->getName()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname()]));
        } else {
            $sess->clrFlag(SessionInterface::FLAG_PINGING);
        }//TODO flag set in server ping timer
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
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_IS_ON, [$sess->getNickname(), ...$resp]));
        }
    }

    public function handleINFO(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleTIME(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname(), $cmd->getArg(0)]));
        } else {
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_TIME, [
                $sess->getNickname(),
                $sess->getServername()
            ], date(DATE_ATOM)));
        }
    }

    public function handleADMIN(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_ADMIN_ME, [$sess->getNickname(), $sess->getServername()]));
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_ADMIN_LOC1, [$sess->getNickname(), $this->config->getAdminLocation1()]));
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_ADMIN_LOC2, [$sess->getNickname(), $this->config->getAdminLocation2()]));
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_ADMIN_ME, [$sess->getNickname(), $this->config->getAdminEmail()]));
        }
    }

    public function handleUSERHOST(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            $resp = [];
            foreach ($cmd->getArgs() as $arg) {
                if ([, $s] = $this->sessions->searchByName($arg)) {
                    $resp[] = $arg
                        . ($s->hasFlag(SessionInterface::FLAG_IS_OPERATOR) ? '*' : '')
                        . ($s->hasFlag(SessionInterface::FLAG_AWAY) ? '=-@' : '=+@')
                        . $s->getHostname();
                }
            }
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_USER_HOST, [$sess->getNickname(), ...$resp]));
        }
    }

    public function handleVERSION(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname(), $cmd->getArg(0)]));
        } else {
            $conn->sendRPL(new RPL($sess->getServername(), RPL::RPL_VERSION, [
                $sess->getNickname(),
                $this->config->getVersionNumber() . '.' . $this->config->getVersionDebug(),
                $this->config->getName()
            ], $this->config->getVersionComment() ?: null));
        }
    }

    public function handleWALLOPS(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_PRIVILEGES, [$sess->getNickname()]));
        } elseif ($cmd->numArgs() === 0) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            foreach ($this->sessions as [$c, $s]) {
                if ($s->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
                    $c->sendCMD(new CMD($cmd->getCode(), [], $cmd->getArg(0), $s->getPrefix()));
                }
            }
        }
    }
}