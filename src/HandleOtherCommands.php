<?php

namespace PE\Component\IRC;

trait HandleOtherCommands
{
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
}