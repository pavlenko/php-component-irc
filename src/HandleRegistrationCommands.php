<?php

namespace PE\Component\IRC;

trait HandleRegistrationCommands
{
    private function handleRegistration(SessionInterface $sess): void
    {
        if (
            //!$sess->hasFlag(SessionInterface::FLAG_CAP_RESOLVED) ||
            empty($sess->getNickname()) || empty($sess->getUsername())
        ) {
            return;
        }
        if (!empty($this->config(Config::CFG_PASSWORD)) && $this->config(Config::CFG_PASSWORD) !== $sess->getPassword()) {
            $sess->close();
            return;
        }
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return;
        }

        $sess->setFlag(SessionInterface::FLAG_REGISTERED);
        $sess->sendRPL(RPL::RPL_WELCOME);
        $sess->sendRPL(RPL::RPL_YOUR_HOST, [], sprintf(
            "Your host is %s, running version %s",
            $this->config(Config::CFG_SERVER_NAME),
            $this->config(Config::CFG_VERSION_NUMBER)
        ));
        $sess->sendRPL(RPL::RPL_CREATED, [], "This server was created {$this->config(Config::CFG_CREATED_AT)}");
        $sess->sendRPL(RPL::RPL_MY_INFO, [
            $this->config(Config::CFG_SERVER_NAME),
            $this->config(Config::CFG_VERSION_NUMBER),
            implode(['i', 'o', 's', 'w']),
            implode(['b', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 's', 't', 'v']),
            implode(['b', 'k', 'l', 'o', 'v']),
        ]);
        //TODO isupport (005, replaced bounce reply)
        //TODO luserclient (251)
        //TODO luserop (252)
        //TODO luserunknown (253)
        //TODO luserchannels (254)
        //TODO luserme (255)
        //TODO localusers (265)-advanced
        //TODO globalusers (266)-advanced
        //TODO statsconn (250)-advanced
        $this->handleMOTD(new CMD(CMD::CMD_MOTD, [$sess->getServername()]), $sess);
    }

    /*public function handleCAP(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            switch ($cmd->getArg(0)) {
                case 'LS':
                    $sess->clrFlag(SessionInterface::FLAG_CAP_RESOLVED);// <-- check capabilities started
                    $sess->sendCMD(CMD::CMD_CAP, ['*', 'LS'], '');//<-- no capabilities
                    break;
                case 'LIST':
                    $sess->sendCMD(CMD::CMD_CAP, ['*', 'LIST'], '');//<-- no capabilities
                    break;
                case 'REQ':
                    $sess->sendCMD(CMD::CMD_CAP, ['*', 'NAK'], $cmd->getComment());//<-- no capabilities
                    break;
                case 'END':
                    $sess->setFlag(SessionInterface::FLAG_CAP_RESOLVED);// <-- check capabilities ended
            }
        }
        $this->handleRegistration($sess);
    }*/

    public function handlePASS(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() === 0) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            $sess->sendERR(ERR::ERR_ALREADY_REGISTERED);
        } else {
            $sess->setPassword($cmd->getArg(0));
        }
    }

    public function handleNICK(CMD $cmd, SessionInterface $sess): void
    {
        if (empty($cmd->getArg(0))) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif (!$this->isValidSessionName($cmd->getArg(0))) {
            $sess->sendERR(ERR::ERR_ERRONEOUS_NICKNAME, [$cmd->getArg(0)]);
        } elseif ($this->storage->sessions()->containsName($cmd->getArg(0))) {
            $sess->sendERR(ERR::ERR_NICKNAME_IN_USE, [$cmd->getArg(0)]);
        } else {
            if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
                foreach ($sess->channels() as $channel) {
                    foreach ($channel->sessions() as $user) {
                        $user->sendCMD($cmd->getCode(), [$cmd->getArg(0)], null, $sess->getPrefix());
                    }
                }
                $this->history->addSession($sess);
            }
            $sess->setNickname($cmd->getArg(0));
        }
        $this->handleRegistration($sess);
    }

    public function handleUSER(CMD $cmd, SessionInterface $sess): void
    {
        if (count($cmd->getArgs()) < 3 || empty($cmd->getComment())) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            $sess->sendERR(ERR::ERR_ALREADY_REGISTERED);
        } else {
            $sess->setUsername($cmd->getArg(0));
            $sess->setRealname($cmd->getComment());
        }
        $this->handleRegistration($sess);
    }

    public function handleOPER(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 2) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } elseif (count($this->config(Config::CFG_OPERATORS)) === 0) {
            $sess->sendERR(ERR::ERR_NO_OPERATOR_HOST);
        } elseif (hash('sha256', (string) $cmd->getArg(1)) === ($this->config(Config::CFG_OPERATORS)[$cmd->getArg(0)] ?? null)) {
            $sess->sendERR(ERR::ERR_PASSWORD_MISMATCH);
        } else {
            $sess->setFlag($sess::FLAG_IS_OPERATOR);
            $sess->sendRPL(RPL::RPL_YOU_ARE_OPERATOR);
        }
    }

    public function handleQUIT(CMD $cmd, SessionInterface $sess): void
    {
        if ($cmd->numArgs() > 0) {
            $sess->setQuitMessage(implode(' ', $cmd->getArgs()));
        }
        foreach ($sess->channels() as $chan) {
            $chan->sessions()->detach($sess);
            $chan->speakers()->detach($sess);
            $chan->operators()->detach($sess);

            $sess->channels()->detach($chan);
            if (count($chan->sessions()) === 0) {
                $this->storage->channels()->detach($chan);
            }
            /*foreach ($channel->sessions() as $user) {
                $user->sendCMD($cmd->getCode(), [$cmd->getArg(0)], null, $sess->getPrefix());
            }*/
        }
        $this->history->addSession($sess);
        $sess->close();
    }
}