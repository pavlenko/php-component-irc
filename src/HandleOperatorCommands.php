<?php

namespace PE\Component\IRC;

trait HandleOperatorCommands
{
    //TODO helpers
    public function handleKILL(CMD $cmd, Connection $conn): void{}
    public function handleREHASH(CMD $cmd, Connection $conn): void{}
    public function handleRESTART(CMD $cmd, Connection $conn): void{}
}