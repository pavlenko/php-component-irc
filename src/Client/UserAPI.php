<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class UserAPI
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
    public function OPER(string $name, string $password): void
    {
        $this->connection->send(new CMD(CMD::OPERATOR, [$name, $password]));
        $this->connection->wait(RPL::YOU_ARE_OPERATOR);
    }

    // roles: REGISTERED
    public function MODE(string $nickname, string $modes): void
    {
        $this->connection->send(new CMD(CMD::MODE, [$nickname, $modes]));
        $this->connection->wait(RPL::USER_MODE_IS);
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
