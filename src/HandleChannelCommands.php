<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    //TODO helpers
    public function handleMODE(CMD $cmd, Connection $conn): void
    {}

    public function handleJOIN(CMD $cmd, Connection $conn): void
    {}

    public function handleTOPIC(CMD $cmd, Connection $conn): void
    {}

    public function handleINVITE(CMD $cmd, Connection $conn): void
    {}

    public function handleKICK(CMD $cmd, Connection $conn): void
    {}

    public function handlePART(CMD $cmd, Connection $conn): void
    {}

    public function handleNAMES(CMD $cmd, Connection $conn): void
    {}

    public function handleLIST(CMD $cmd, Connection $conn): void
    {}
}