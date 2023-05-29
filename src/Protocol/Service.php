<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;

class Service
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function register(?string $password, string $nickname, string $servers, string $info): void
    {
        if (!empty($password)) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$password]));
        }

        $this->connection->send(new CMD(CMD::SERVICE, [$nickname, 0, $servers, 0, 0], $info));

        //TODO wait for YOU_ARE_SERVICE
    }
}
