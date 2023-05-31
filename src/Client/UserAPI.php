<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class UserAPI
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    // roles: REGISTERED
    public function QUIT(string $message = null): void
    {
        $this->connection->send(new CMD(CMD::QUIT, [], $message));
        $this->connection->wait(CMD::ERROR);// this ack by error command
    }

    // roles: REGISTERED|IRC_OPERATOR
    public function S_QUIT(string $server, string $comment): void
    {
        $this->connection->send(new CMD(CMD::QUIT, [$server], $comment));
        $this->connection->wait(CMD::WALLOPS);
    }
}
