<?php

namespace PE\Component\IRC\Event;

use PE\Component\IRC\ERR;
use PE\Component\IRC\Protocol\Connection;

final class ErrorEvent
{
    private Connection $connection;
    private ERR $error;

    public function __construct(Connection $connection, ERR $error)
    {
        $this->connection = $connection;
        $this->error = $error;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getError(): ERR
    {
        return $this->error;
    }
}
