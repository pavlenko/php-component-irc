<?php

namespace PE\Component\IRC;

trait HandleRegistrationCommands
{
    //TODO helpers
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
    {}

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