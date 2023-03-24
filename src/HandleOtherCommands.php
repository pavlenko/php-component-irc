<?php

namespace PE\Component\IRC;

trait HandleOtherCommands
{
    //TODO helpers
    public function handlePING(CMD $cmd, Connection $conn): void
    {}

    public function handlePONG(CMD $cmd, Connection $conn): void
    {}

    public function handleISON(CMD $cmd, Connection $conn): void
    {}

    public function handleINFO(CMD $cmd, Connection $conn): void
    {}

    public function handleTIME(CMD $cmd, Connection $conn): void
    {}

    public function handleADMIN(CMD $cmd, Connection $conn): void
    {}

    public function handleUSERHOST(CMD $cmd, Connection $conn): void
    {}

    public function handleVERSION(CMD $cmd, Connection $conn): void
    {}

    public function handleWALLOPS(CMD $cmd, Connection $conn): void
    {}
}