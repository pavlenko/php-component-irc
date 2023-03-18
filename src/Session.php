<?php

namespace PE\Component\IRC;

use React\Socket\ConnectionInterface;

class Session
{
    private array $data;
    private ConnectionInterface $conn;
    private Server $server;

    public function __construct(ConnectionInterface $conn, Server $server, array $data = [])
    {
        $this->data   = $data;
        $this->conn   = $conn;
        $this->server = $server;
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
        $this->server->processMessageSend($this->conn, $command);
        $this->conn->write($command);
    }
}