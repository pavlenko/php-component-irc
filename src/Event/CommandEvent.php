<?php

namespace PE\Component\IRC\Event;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Protocol\Connection;

final class CommandEvent
{
    private Connection $connection;
    private CMD $command;

    public function __construct(Connection $connection, CMD $command)
    {
        $this->connection = $connection;
        $this->command = $command;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getCommand(): CMD
    {
        return $this->command;
    }
}
