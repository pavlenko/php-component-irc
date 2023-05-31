<?php

namespace PE\Component\IRC\Event;

use PE\Component\IRC\Protocol\Connection;

/* @deprecated */
final class ConnectedEvent
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
