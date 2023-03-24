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
                    //TODO continue registration
                    break;
            }
        }
    }

    public function handlePASS(CMD $cmd, Connection $conn): void
    {}

    public function handleNICK(CMD $cmd, Connection $conn): void
    {}

    public function handleUSER(CMD $cmd, Connection $conn): void
    {}

    public function handleOPER(CMD $cmd, Connection $conn): void
    {}

    public function handleQUIT(CMD $cmd, Connection $conn): void
    {}
}