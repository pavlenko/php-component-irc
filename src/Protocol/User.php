<?php

namespace PE\Component\IRC\Protocol;

class User
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function register(?string $password, string $nickname, string $username, string $realname, int $flags): void
    {
        //TODO register and fill session data
    }
}
