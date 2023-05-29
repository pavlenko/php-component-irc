<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class ChannelsAPI
{
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    // roles: REGISTERED
    public function JOIN(string $channel, string $key = null): void
    {
        $this->connection->send(new CMD(CMD::JOIN, [$channel, $key]));
        $this->connection->wait(CMD::JOIN);// wait for JOIN with own nick and channel
    }

    // roles: REGISTERED
    public function PART(string $channel): void
    {
        $this->connection->send(new CMD(CMD::PART, [$channel]));
        $this->connection->wait(CMD::PART);// wait for PART with own nick and channel
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function MODE(string $channel, string $modes, string $params): void
    {
        $this->connection->send(new CMD(CMD::MODE, [$channel, $modes, $params]));
        //TODO response dependent on passed arguments, so better split to some parts
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function TOPIC(string $channel, string $topic = null): void
    {
        $this->connection->send(new CMD(CMD::TOPIC, [$channel], $topic));
        $this->connection->wait([RPL::TOPIC, RPL::NO_TOPIC]);//TODO allow 2 possible responses
    }

    // roles: REGISTERED
    public function NAMES(string $channel, string $server = null): void
    {
        $this->connection->send(new CMD(CMD::NAMES, [$channel, $server]));
        $this->connection->wait(RPL::NAMES_REPLY);
    }

    // roles: REGISTERED
    public function LIST(string $channel, string $server = null): void
    {
        $this->connection->send(new CMD(CMD::LIST, [$channel, $server]));
        $this->connection->wait([RPL::LIST_START, RPL::LIST]);//TODO maybe list_start is better
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function INVITE(string $channel, string $nickname): void
    {
        $this->connection->send(new CMD(CMD::INVITE, [$nickname, $channel]));
        $this->connection->wait([RPL::INVITING, RPL::AWAY]);//TODO are both can be received at once?
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function KICK(string $channel, string $nickname, string $comment = null): void
    {
        $this->connection->send(new CMD(CMD::KICK, [$channel, $nickname], $comment));
        $this->connection->wait(CMD::KICK);//TODO also check if issuer match current user
    }
}
