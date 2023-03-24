<?php

namespace PE\Component\IRC;

trait HandleRegistrationCommands
{
    //TODO helpers
    public function handleCAP(CMD $cmd){}//<-- additional but used
    public function handlePASS(CMD $cmd){}
    public function handleNICK(CMD $cmd){}
    public function handleUSER(CMD $cmd){}
    public function handleOPER(CMD $cmd){}
    public function handleQUIT(CMD $cmd){}
}