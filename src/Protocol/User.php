<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\RPL;

class User
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function register(?string $password, string $nickname, string $username, string $realname, int $flags): Deferred
    {
        if (!empty($password)) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$password]));
        }

        $this->connection->send(new CMD(CMD::NICK, [$nickname]));
        $this->connection->send(new CMD(CMD::USER, [$username, $flags, '*'], $realname));

        return $this->connection->wait(RPL::WELCOME);
    }
}
