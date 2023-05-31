<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Util\Deferred;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class ClientAPI
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    // roles: GUEST|REGISTERED
    public function NICK(string $nick): Deferred
    {
        //TODO this method only for change nick only so check for registered flag
        $this->connection->send(new CMD(CMD::NICK, [$nick]));
        return $this->connection->wait(CMD::NICK)->deferred();
    }

    // roles: REGISTERED
    public function AWAY(string $message = null): Deferred
    {
        $deferred = new Deferred();

        $this->connection->send(new CMD(CMD::AWAY, [], $message));
        $this->connection->wait(RPL::UN_AWAY, RPL::NOW_AWAY)
            ->deferred()
            ->then(fn($msg) => $deferred->resolved($msg))
            ->else(fn($err) => $deferred->rejected($err));

        return $deferred;
    }

    // roles: IRC_OPERATOR
    public function REHASH(): void
    {
        $this->connection->send(new CMD(CMD::REHASH));
        $this->connection->wait(RPL::REHASHING);
    }

    // roles: IRC_OPERATOR
    public function DIE(): void
    {
        $this->connection->send(new CMD(CMD::DIE));
        //TODO no response???
    }

    public function RESTART(): void
    {
        $this->connection->send(new CMD(CMD::RESTART));
        //TODO no response???
    }

    public function SUMMON(string $user, string $target = null, string $channel = null): void
    {
        //TODO can be disabled
        //TODO disable this possibility
        $this->connection->send(new CMD(CMD::SUMMON, [$user, $target, $channel]));
        $this->connection->wait(RPL::SUMMONING);
    }

    public function USERS(string $target = null): void
    {
        //TODO can be disabled
        //TODO used to list system users, no irc users
        $this->connection->send(new CMD(CMD::USERS, [$target]));
        $this->connection->wait([
            RPL::USERS_START,
            RPL::USERS,
            RPL::NO_USERS,
            RPL::END_OF_USERS,
        ]);
    }

    public function WALLOPS(string $message): void
    {
        $this->connection->send(new CMD(CMD::WALLOPS, [], $message));
        //TODO no reply???
    }

    public function USERHOST(string ...$nicknames): void
    {
        $this->connection->send(new CMD(CMD::USER_HOST, $nicknames));
        $this->connection->wait(RPL::USER_HOST);
    }

    public function ISON(string ...$nicknames): void
    {
        $this->connection->send(new CMD(CMD::IS_ON, $nicknames));
        $this->connection->wait(RPL::IS_ON);
    }
}
