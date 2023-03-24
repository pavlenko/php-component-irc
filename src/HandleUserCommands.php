<?php

namespace PE\Component\IRC;

trait HandleUserCommands
{
    //TODO helpers
    public function handlePRIVMSG(CMD $cmd, Connection $conn): void
    {}

    public function handleNOTICE(CMD $cmd, Connection $conn): void
    {}

    public function handleAWAY(CMD $cmd, Connection $conn): void
    {}

    public function handleWHO(CMD $cmd, Connection $conn): void
    {}

    public function handleWHOIS(CMD $cmd, Connection $conn): void
    {}

    public function handleWHOWAS(CMD $cmd, Connection $conn): void
    {}
}