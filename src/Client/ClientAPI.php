<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class ClientAPI
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    // roles: REGISTERED
    public function away(string $message = null): void
    {
        $this->connection->send(new CMD(CMD::AWAY, [], $message));
        $this->connection->wait([
            RPL::UN_AWAY,
            RPL::NOW_AWAY,
        ]);
    }

    // roles: IRC_OPERATOR
    public function rehash(): void
    {
        $this->connection->send(new CMD(CMD::REHASH));
        $this->connection->wait(RPL::REHASHING);
    }

    // roles: IRC_OPERATOR
    public function die(): void
    {
        $this->connection->send(new CMD(CMD::DIE));
        //TODO no response???
    }

    public function restart(): void
    {
        $this->connection->send(new CMD(CMD::RESTART));
        //TODO no response???
    }

    public function summon(string $user, string $target = null, string $channel = null): void
    {
        //TODO disable this possibility
        $this->connection->send(new CMD(CMD::SUMMON, [$user, $target, $channel]));
        $this->connection->wait(RPL::SUMMONING);
    }
}
