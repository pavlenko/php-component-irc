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

    //TODO server api....
    // roles: REGISTERED|IRC_OPERATOR
    public function S_QUIT(string $server, string $comment): void
    {
        $this->connection->send(new CMD(CMD::QUIT, [$server], $comment));
        $this->connection->wait(CMD::WALLOPS);
    }
}
