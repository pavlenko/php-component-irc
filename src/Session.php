<?php

namespace PE\Component\IRC;

use React\Socket\ConnectionInterface;

/**
 * @property int $flags
 * @property string $password
 * @property string $nickname
 * @property string $username
 * @property string $realname
 * @property string $hostname
 * @property string $servername
 */
class Session
{
    public const REGISTERED       = 0b00000001;
    public const INVISIBLE        = 0b00000010;
    public const RECEIVE_NOTICE   = 0b00000100;
    public const RECEIVE_WALLOPS  = 0b00001000;
    public const IRC_OPERATOR     = 0b00010000;
    public const AWAY             = 0b00100000;
    public const PINGING          = 0b01000000;
    public const BREAK_CONNECTION = 0b10000000;

    private array $data;
    private ConnectionInterface $connection;
    private Server $server;

    public function __construct(ConnectionInterface $connection, Server $server, array $data = [])
    {
        $this->data = $data;
        $this->connection = $connection;
        $this->server = $server;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function __get(string $name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }

    public function send(Command $command): void
    {
        $this->server->processMessageSend($this->connection, $command);
    }

    public function quit(): void
    {
        $this->connection->close();
    }
}