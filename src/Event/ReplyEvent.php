<?php

namespace PE\Component\IRC\Event;

use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

final class ReplyEvent
{
    private Connection $connection;
    private RPL $reply;

    public function __construct(Connection $connection, RPL $reply)
    {
        $this->connection = $connection;
        $this->reply = $reply;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getReply(): RPL
    {
        return $this->reply;
    }
}
