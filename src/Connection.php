<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use React\Socket\ConnectionInterface as SocketConnection;

final class Connection
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

    /**
     * Decode message to specific command
     *
     * @param string $input
     * @internal
     */
    public function onInput(string $input): void
    {
        $this->buffer .= $input;

        while (($len = strlen($this->buffer)) > 0) {
            $pos  = strpos($this->buffer, "\n");
            $line = substr($this->buffer, 0, $pos ?: $len);
            $line = trim($line);

            try {
                $msg = $this->decode($line);
                $this->logger->log(LogLevel::INFO, '< ' . $msg->toLogger());
                $this->events->trigger(self::EVT_INPUT, $msg);
            } catch (\Throwable $error) {
                $this->events->trigger(self::EVT_ERROR, $error, $line);
            }

            $this->buffer = substr($this->buffer, $pos);
        }
    }

    private function decode(string $data)
    {
        $parts = preg_split('/\s+/', $data, 2, PREG_SPLIT_NO_EMPTY);

        // Resolve prefix
        $prefix = null;
        if (!empty($parts) && ':' === $parts[0][0]) {
            $prefix = substr($parts[0], 1);
            $data   = $parts[1] ?? '';
        }

        // Resolve command
        $parts = preg_split('/\s+/', $data, 2, PREG_SPLIT_NO_EMPTY);
        $code  = strtoupper(array_shift($parts) ?? '');

        if (empty($code)) {
            throw new \UnexpectedValueException('Malformed data, no command code exists');
        }

        // Resolve comment & params
        $parts   = preg_split('/:/', $parts[0] ?? '', 2, PREG_SPLIT_NO_EMPTY);
        $args    = preg_split('/\s+/', $parts[0] ?? '', null, PREG_SPLIT_NO_EMPTY);
        $comment = !empty($parts[1]) ? trim($parts[1]) : null;

        if (is_numeric($code)) {
            if ($code < 400) {
                return new RPL($prefix, $code, $args, $comment);
            }
            return new ERR($prefix, $code, $args, $comment);
        }
        return new CMD($code, $args, $comment, $prefix);
    }

    public function sendCMD(CMD $cmd): bool
    {
        $this->logger->log(LogLevel::NOTICE, '> ' . $cmd->toLogger());
        return $this->socket->write($cmd->toString() . "\r\n");
    }

    public function sendERR(ERR $err): bool
    {
        $this->logger->log(LogLevel::ERROR, '> ' . $err->toLogger());
        return $this->socket->write($err->toString() . "\r\n");
    }

    public function sendRPL(RPL $rpl): bool
    {
        $this->logger->log(LogLevel::NOTICE, '> ' . $rpl->toLogger());
        return $this->socket->write($rpl->toString() . "\r\n");
    }

    public function close(): void
    {
        $this->logger->log(LogLevel::NOTICE, '> ' . self::EVT_CLOSE);
        $this->socket->close();
    }
}
