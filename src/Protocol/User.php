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

    //TODO if registered - protocol exception
    public function register(?string $pass, string $nick, string $user, string $realname, int $flags): Deferred
    {
        if (!empty($pass)) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$pass]));
        }

        $this->connection->send(new CMD(CMD::NICK, [$nick]));
        $this->connection->send(new CMD(CMD::USER, [$user, $flags, '*'], $realname));

        return $this->connection->wait(RPL::WELCOME);
    }

    // roles: GUEST|REGISTERED
    public function NICK(string $nick): void
    {
        $this->connection->send(new CMD(CMD::NICK, [$nick]));
    }

    // roles: REGISTERED
    public function OPERATOR(): void
    {}

    // roles: REGISTERED
    public function MODE(): void
    {}

    // roles: REGISTERED
    public function QUIT(): void
    {}

    // roles: REGISTERED|IRC_OPERATOR
    public function SERVER_QUIT(): void
    {}
}
