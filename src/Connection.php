<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\Socket\ConnectionInterface as SocketConnection;

final class Connection implements ConnectionInterface, EventsInterface
{
    private SocketConnection $socket;
    private EventsInterface $events;
    private LoggerInterface $logger;

    public function __construct(SocketConnection $socket, LoggerInterface $logger = null, EventsInterface $events = null)
    {
        $this->socket = $socket;
        $this->events = $events ?: new Events();
        $this->logger = $logger ?: new NullLogger();
        $this->logger->notice('New connection from ' . $socket->getRemoteAddress());

        $this->socket->on('data', [$this, 'onInput']);
        $this->socket->on('error', fn($error) => $this->events->trigger(self::EVT_ERROR, $error));
        $this->socket->on('close', fn() => $this->events->trigger(self::EVT_CLOSE));
    }

    public function attach(string $event, callable $listener, int $priority = 0): void
    {
        $this->events->attach($event, $listener, $priority);
    }

    public function detach(string $event, callable $listener): void
    {
        $this->events->detach($event, $listener);
    }

    public function trigger(string $event, ...$arguments): int
    {
        return $this->events->trigger($event, ...$arguments);
    }

    /**
     * Decode message to specific command
     *
     * @param string $input
     * @internal
     */
    public function onInput(string $input): void
    {
        $lines = preg_split('/\n/', $input, 0, PREG_SPLIT_NO_EMPTY);
        foreach ($lines as $line) {
            try {
                $msg = $this->decode(trim($line));
                $this->logger->notice('< ' . $msg->toLogger());
                $this->events->trigger(self::EVT_INPUT, $msg);
            } catch (\Throwable $error) {
                $this->events->trigger(self::EVT_ERROR, $error, $line);
            }
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
        $args    = preg_split('/\s+/', $parts[0] ?? '', 0, PREG_SPLIT_NO_EMPTY);
        $comment = !empty($parts[1]) ? trim($parts[1]) : null;

        if (is_numeric($code)) {
            if ($code < 400) {
                return new RPL($prefix, $code, $args, $comment);
            }
            return new ERR($prefix, $code, $args, $comment);
        }
        return new CMD($code, $args, $comment, $prefix);
    }

    public function write(MSG $msg): bool
    {
        $this->logger->notice('> ' . $msg->toLogger());
        return $this->socket->write($msg->toString() . "\r\n");
    }

    public function close(): void
    {
        $this->logger->notice('Close connection from ' . $this->socket->getRemoteAddress());
        $this->socket->close();
    }
}
