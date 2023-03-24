<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use React\Socket\ConnectionInterface as SocketConnection;

class Connection
{
    public const EVT_ERROR = 'conn.error';
    public const EVT_CLOSE = 'conn.close';
    public const EVT_INPUT = 'conn.input';

    private string $buffer = '';

    private SocketConnection $socket;
    private EventsInterface $events;
    private LoggerInterface $logger;

    public function __construct(SocketConnection $socket, EventsInterface $events, LoggerInterface $logger = null)
    {
        $this->socket = $socket;
        $this->events = $events;
        $this->logger = $logger ?: new NullLogger();

        $this->socket->on('input', [$this, 'onInput']);
        $this->socket->on('error', fn($error) => $this->events->trigger(self::EVT_ERROR, $error));
        $this->socket->on('close', fn() => $this->events->trigger(self::EVT_CLOSE));
    }

    public function onInput(string $input): void
    {
        $this->buffer .= $input;

        while (($len = strlen($this->buffer)) > 0) {
            $pos  = strpos($this->buffer, "\n");
            $line = substr($this->buffer, 0, $pos ?: $len);

            //TODO parse input here

            $this->logger->log(LogLevel::INFO, '< ' . trim($line));
            $this->events->trigger(self::EVT_INPUT, trim($line));
            $this->buffer = substr($this->buffer, $pos);
        }
    }

    public function sendCMD(CMD $cmd): void
    {
        $this->logger->log(LogLevel::NOTICE, '> CMD:' . $cmd);
        $this->socket->write($cmd . "\r\n");
    }

    public function sendERR(ERR $err)
    {
        $this->logger->log(LogLevel::ERROR, '> ERR:' . $err);
        $this->socket->write($err . "\r\n");
    }

    public function sendRPL(RPL $rpl)
    {
        $this->logger->log(LogLevel::NOTICE, '> RPL:' . $rpl);
        $this->socket->write($rpl . "\r\n");
    }

    public function close(): void
    {
        $this->logger->log(LogLevel::NOTICE, '> ' . self::EVT_CLOSE);
        $this->socket->close();
    }
}

class CMD
{
    public function __toString(): string
    {
        return '';// TODO: Implement __toString() method.
    }
}

class ERR
{
    public function __toString(): string
    {
        return '';// TODO: Implement __toString() method.
    }
}

class RPL
{
    public function __toString(): string
    {
        return '';// TODO: Implement __toString() method.
    }
}