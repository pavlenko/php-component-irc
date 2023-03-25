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

    public function handleCAP(CMD $cmd, Connection $conn)
    {
        $sess = $this->sessions[$conn];
        if ($cmd->numArgs() < 1) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            switch ($cmd->getArg(0)) {
                case 'LS':
                    $conn->sendCMD(new CMD(CMD::CMD_CAP, ['*', 'LS'], ''));//<-- no capabilities
                    break;
                case 'LIST':
                    $conn->sendCMD(new CMD(CMD::CMD_CAP, ['*', 'LIST'], ''));//<-- no capabilities
                    break;
                case 'REQ':
                    $conn->sendCMD(new CMD(CMD::CMD_CAP, ['*', 'NAK'], $cmd->getComment()));//<-- no capabilities
                    break;
                case 'END':
                    //TODO set session capabilities resolved flag
                    break;
            }
        }
        //TODO continue registration
    }

    public function handlePASS(CMD $cmd, Connection $conn): void
    {
        $sess = $this->sessions[$conn];
        if ($cmd->numArgs() === 0) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } elseif ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_ALREADY_REGISTERED, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            $sess->setPassword($cmd->getArg(0));
        }
    }

    public function handleNICK(CMD $cmd, Connection $conn): void
    {
        $sess = $this->sessions[$conn];
        if (empty($cmd->getArg(0))) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NEED_MORE_PARAMS, [$sess->getNickname(), $cmd->getCode()]));
        } elseif (!$this->isValidSessionName($cmd->getArg(0))) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_ERRONEOUS_NICKNAME, [$sess->getNickname(), $cmd->getCode()]));
        } elseif ($this->sessions->containsName($cmd->getArg(0))) {
            $conn->sendERR(new ERR($this->config->getName(), ERR::ERR_NICKNAME_IN_USE, [$sess->getNickname(), $cmd->getCode()]));
        } else {
            if ($sess->hasFlag(SessionInterface::FLAG_REGISTERED)) {
                //TODO notify users
                //TODO upd history
            }
            $sess->setNickname($cmd->getArg(0));
        }
        //TODO continue registration
    }

    public function handleUSER(CMD $cmd, Connection $conn): void
    {
        $sess = $this->sessions[$conn];
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

    public function handleOPER(CMD $cmd, Connection $conn): void
    {
        $sess = $this->sessions[$conn];
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

    public function handleQUIT(CMD $cmd, Connection $conn): void
    {
        if ($cmd->getArg(0)) {
            $sess = $this->sessions[$conn];
            $sess->setQuitMessage($cmd->getArg(0));
        }
        //TODO add nickname to history
        $conn->close();
    }
}