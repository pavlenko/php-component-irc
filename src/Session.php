<?php

namespace PE\Component\IRC;

use React\Socket\ConnectionInterface;

class Session
{
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