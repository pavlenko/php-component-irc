<?php

namespace PE\Component\IRC;

trait HandleRegistrationCommands
{
    private function isValidChannelName(string $name): bool
    {
        if (strlen($name) > 50) {
            $this->logger->debug('Session name must be less than 51 chars');
            return false;
        }
        if (!preg_match('/^[#@+!].+$/', $name)) {
            $this->logger->debug('Channel name must starts with "#", "@", "+" or "!"');
            return false;
        }
        if (!preg_match('/^[\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Channel name contain invalid chars');
            return false;
        }

        return false;
    }

    private function isValidSessionName(string $name): bool
    {
        if (strlen($name) > 9) {
            $this->logger->debug('Session name must be less than 10 chars');
            return false;
        }
        if (preg_match('/^[^0-9-].+$/', $name)) {
            $this->logger->debug('Session name must not starts with number or "-"');
            return false;
        }
        if (!preg_match('/^[\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Session name contain invalid chars');
            return false;
        }
        if ($this->config->getName() === $name) {
            $this->logger->debug('Session name must not equal server name');
            return false;
        }
        return true;
    }

    private function handleRegistration(SessionInterface $sess): void
    {
        if (
            !$sess->hasFlag(SessionInterface::FLAG_CAP_RESOLVED) ||
            empty($sess->getNickname()) || empty($sess->getUsername())
        ) {
            return;
        }

        if (!empty($this->config->getPassword()) && $this->config->getPassword() !== $sess->getPassword()) {
            $sess->close();
            return;
        }

        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return;
        }

        $sess->setFlag(SessionInterface::FLAG_REGISTERED);
        $sess->sendRPL(RPL::RPL_WELCOME);
        $sess->sendRPL(RPL::RPL_YOUR_HOST, [], "Your host is {$this->config->getName()}, running version {$this->config->getVersionNumber()}");
        $sess->sendRPL(RPL::RPL_CREATED, [], "This server was created {$this->config->getCreatedAt()->format(DATE_ATOM)}");
        $sess->sendRPL(RPL::RPL_MY_INFO, [
            $sess->getNickname(),
            $this->config->getName(),
            $this->config->getVersionNumber(),
            implode(['i', 'o', 's', 'w']),
            implode(['b', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 's', 't', 'v']),
            implode(['b', 'k', 'l', 'o', 'v']),
        ]);
        $this->handleMOTD(new CMD(CMD::CMD_MOTD, [$sess->getServername()]), $sess);
    }

    public function handleCAP(CMD $cmd, SessionInterface $sess): bool
    {
        if ($cmd->numArgs() < 1) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }

        switch ($cmd->getArg(0)) {
            case 'LS':
                $sess->clrFlag(SessionInterface::FLAG_CAP_RESOLVED);// <-- check capabilities started
                return $sess->sendCMD(CMD::CMD_CAP, ['*', 'LS'], '');//<-- no capabilities
            case 'LIST':
                return $sess->sendCMD(CMD::CMD_CAP, ['*', 'LIST'], '');//<-- no capabilities
            case 'REQ':
                return $sess->sendCMD(CMD::CMD_CAP, ['*', 'NAK'], $cmd->getComment());//<-- no capabilities
            case 'END':
                $sess->setFlag(SessionInterface::FLAG_CAP_RESOLVED);// <-- check capabilities ended
        }

        $this->handleRegistration($sess);
        return true;
    }

    public function handlePASS(CMD $cmd, SessionInterface $sess): bool
    {
        if ($cmd->numArgs() === 0) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            return $sess->sendERR(ERR::ERR_ALREADY_REGISTERED);
        }
        $sess->setPassword($cmd->getArg(0));
        return true;
    }

    public function handleNICK(CMD $cmd, SessionInterface $sess): bool
    {
        if (empty($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        }
        if (!$this->isValidSessionName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ERR_ERRONEOUS_NICKNAME, [$cmd->getArg(0)]);
        }
        if ($this->sessions->containsName($cmd->getArg(0))) {
            return $sess->sendERR(ERR::ERR_NICKNAME_IN_USE, [$cmd->getArg(0)]);
        }
        if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            $channels = $sess->getChannels();
            foreach ($channels as $channel) {
                foreach ($channel->getSessions() as $user) {
                    $user->sendCMD($cmd->getCode(), [$cmd->getArg(0)], null, $sess->getPrefix());
                }
            }
            $this->history->addSession($sess);
        }
        $sess->setNickname($cmd->getArg(0));
        $this->handleRegistration($sess);
        return true;
    }

    public function handleUSER(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if (count($cmd->getArgs()) < 3 || empty($cmd->getComment())) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } elseif ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_ALREADY_REGISTERED, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            $sess->setUsername($cmd->getArg(0));
            $sess->setRealname($cmd->getComment());
        }
        //TODO continue registration
    }

    public function handleOPER(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 2) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } elseif (count($this->operators) === 0) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NO_OPERATOR_HOST, [$sess->getNickname()]));
        } elseif (hash('sha256', $cmd->getArg(1)) === ($this->operators[$cmd->getArg(0)] ?? null)) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_PASSWORD_MISMATCH, [$sess->getNickname()]));
        } else {
            $sess->setFlag($sess::FLAG_IS_OPERATOR);
            $conn->sendRPL(new RPL($this->config->getName(), RPL::RPL_YOU_ARE_OPERATOR, [$sess->getNickname()]));
        }
    }

    public function handleQUIT(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->getArg(0)) {
            $sess->setQuitMessage($cmd->getArg(0));
        }
        $this->history->addSession($sess);
        //TODO notify users
        $conn->close();
    }
}