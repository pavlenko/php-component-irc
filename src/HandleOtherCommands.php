<?php

namespace PE\Component\IRC;

trait HandleOtherCommands
{
    public function handleMOTD(CMD $cmd, SessionInterface $sess): bool
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER);
        }
        $motd = $this->config(Config::CFG_MOTD_FILE);
        if (null !== $motd && is_readable($motd)) {
            $motd = file($motd, FILE_IGNORE_NEW_LINES) ?: null;
        }
        if (empty($motd)) {
            return $sess->sendERR(ERR::ERR_NO_MOTD);
        }
        $sess->sendRPL(RPL::RPL_MOTD_START, [], '- Message of the day -');
        foreach ($motd as $line) {
            $sess->sendRPL(RPL::RPL_MOTD, [], $line);
        }
        return $sess->sendRPL(RPL::RPL_END_OF_MOTD);
    }

    public function handlePING(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NO_ORIGIN);
        } else {
            $sess->sendCMD(CMD::CMD_PONG, [], $cmd->getArg(0), $sess->getServername());
        }
    }

    public function handlePONG(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0 || $cmd->getArg(0) !== $this->config(Config::CFG_SERVER_NAME)) {
            $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->numArgs()]);
        } else {
            $sess->clrFlag(SessionInterface::FLAG_PINGING);
        }
    }

    public function handleISON(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $resp = [];
            foreach ($cmd->getArgs() as $arg) {
                if ($this->storage->sessions()->containsName($arg)) {
                    $resp[] = $arg;
                }
            }
            $sess->sendRPL(RPL::RPL_IS_ON, [], implode(' ', $resp));
        }
    }

    public function handleINFO(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(0)]);
        } else {
            $sess->sendRPL(RPL::RPL_INFO, [], $this->config(Config::CFG_INFO));
            $sess->sendRPL(RPL::RPL_END_OF_INFO);
        }
    }

    public function handleUSERHOST(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $resp = [];
            foreach ($cmd->getArgs() as $arg) {
                if ($user = $this->storage->sessions()->searchByName($arg)) {
                    $resp[] = $arg
                        . ($user->hasFlag(SessionInterface::FLAG_IS_OPERATOR) ? '*' : '')
                        . '='
                        . ($user->hasFlag(SessionInterface::FLAG_AWAY) ? '-' : '+')
                        . $user->getUsername()
                        . '@'
                        . $user->getHostname();
                }
            }
            $sess->sendRPL(RPL::RPL_USER_HOST, [], implode(' ', $resp));
        }
    }

    public function handleVERSION(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0 && $cmd->getArg(0) !== $sess->getServername()) {
            $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$cmd->getArg(0)]);
        } else {
            $sess->sendRPL(RPL::RPL_VERSION, [
                $this->config(Config::CFG_VERSION_NUMBER) . '.' . $this->config(Config::CFG_VERSION_DEBUG),
                $this->config(Config::CFG_SERVER_NAME)
            ], $this->config(Config::CFG_VERSION_COMMENT) ?: null);
        }
    }

    public function handleWALLOPS(CMD $cmd, SessionInterface $sess): void
    {
        if (!$sess->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
            $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
        } elseif ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            foreach ($this->storage->sessions() as $user) {
                if ($user->hasFlag(SessionInterface::FLAG_IS_OPERATOR)) {
                    $user->sendCMD($cmd->getCode(), [], $cmd->getArg(0));
                }
            }
        }
    }
}