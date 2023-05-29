<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

class ServerAPI
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function MOTD(string $server = null): void
    {
        //TODO read all motd message to single string, maybe check all possible responses
        $this->connection->send(new CMD(CMD::MOTD, [$server]));
        $this->connection->wait([RPL::MOTD_START, RPL::MOTD, RPL::END_OF_MOTD]);
    }

    public function LIST_USERS(string $mask = null, string $server = null): void
    {
        $this->connection->send(new CMD(CMD::LIST_USERS, [$mask, $server]));
        $this->connection->wait([
            RPL::L_USER_CLIENT,
            RPL::L_USER_OPERATORS,
            RPL::L_USER_UNKNOWN,
            RPL::L_USER_CHANNELS,
            RPL::L_USER_ME,
        ]);
    }

    public function VERSION(string $server = null): void
    {
        $this->connection->send(new CMD(CMD::VERSION, [$server]));
        $this->connection->wait(RPL::VERSION);
    }

    public function STATS(string $query = null, string $server = null): void
    {
        $this->connection->send(new CMD(CMD::STATS, [$query, $server]));
        $this->connection->wait([
            RPL::STATS_LINK_INFO,
            RPL::STATS_UPTIME,
            RPL::STATS_COMMANDS,
            RPL::STATS_O_LINE,
            RPL::END_OF_STATS,
            //TODO other possible stats replies, maybe depends on query
        ]);
    }

    public function LINKS(string $mask = null, string $server = null): void
    {
        $this->connection->send(new CMD(CMD::LINKS, [$server, $mask]));
        $this->connection->wait([RPL::LINKS, RPL::END_OF_LINKS]);
    }

    public function TIME(string $server = null): void
    {
        $this->connection->send(new CMD(CMD::TIME, [$server]));
        $this->connection->wait(RPL::TIME);
    }

    // roles: IRC_OPERATOR
    public function CONNECT(string $target, int $port, string $remote = null): void
    {
        $this->connection->send(new CMD(CMD::CONNECT, [$target, $port, $remote]));
        $this->connection->wait(CMD::WALLOPS);// this will receive by all irc network operators
    }

    public function TRACE(string $target = null): void
    {
        $this->connection->send(new CMD(CMD::TRACE, [$target]));
        $this->connection->wait([
            RPL::TRACE_LINK,
            RPL::TRACE_CONNECTING,
            RPL::TRACE_HANDSHAKE,
            RPL::TRACE_UNKNOWN,
            RPL::TRACE_OPERATOR,
            RPL::TRACE_USER,
            RPL::TRACE_SERVER,
            RPL::TRACE_SERVICE,
            RPL::TRACE_NEW_TYPE,
            RPL::TRACE_CLASS,
            RPL::TRACE_LOG,
            RPL::TRACE_END,
        ]);
    }

    public function ADMIN(string $target = null): void
    {
        $this->connection->send(new CMD(CMD::ADMIN, [$target]));
        $this->connection->wait([
            RPL::ADMIN_ME,// - first
            RPL::ADMIN_LOC1,
            RPL::ADMIN_LOC2,
            RPL::ADMIN_EMAIL,// - last
        ]);
    }

    public function INFO(string $target = null): void
    {
        $this->connection->send(new CMD(CMD::INFO, [$target]));
        $this->connection->wait([
            RPL::INFO,// - can repeat
            RPL::END_OF_INFO,// - last
        ]);
    }
}
