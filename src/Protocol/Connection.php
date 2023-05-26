<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\ERR;
use PE\Component\IRC\Exception\TimeoutException;
use PE\Component\IRC\MSG;
use PE\Component\IRC\RPL;

final class Connection
{
    private \PE\Component\Socket\Client $socket;
    private int $responseTimeout;
    private int $inactiveTimeout;
    private int $lastMessageTime = 0;

    private \Closure $onInput;
    private \Closure $onWrite;
    private \Closure $onError;
    private \Closure $onClose;

    /**
     * @var Deferred[]
     */
    private array $waitQueue = [];

    public function __construct(
        \PE\Component\Socket\Client $socket,
        int $responseTimeout = Config::DEFAULT_RESPONSE_TIMEOUT,
        int $inactiveTimeout = Config::DEFAULT_INACTIVE_TIMEOUT
    ) {
        $this->responseTimeout = $responseTimeout;
        $this->inactiveTimeout = $inactiveTimeout;

        $this->onInput = fn() => null;
        $this->onWrite = fn() => null;
        $this->onError = fn() => null;
        $this->onClose = fn() => null;

        $this->socket = $socket;
        $this->socket->setCloseHandler(fn(string $message = null) => $this->close($message));
        $this->socket->setErrorHandler(fn(\Throwable $e) => call_user_func($this->onError, $e));
        $this->socket->setInputHandler(function (string $data) {
            try {
                $lines = preg_split('/\n/', $data, 0, PREG_SPLIT_NO_EMPTY);
                foreach ($lines as $line) {
                    try {
                        $msg = $this->decode(trim($line));
                        call_user_func($this->onInput, $msg);
                    } catch (\Throwable $error) {
                        call_user_func($this->onError, $error, $line);
                    }
                }
            } catch (\Throwable $exception) {
                call_user_func($this->onError, $exception);
            }
        });
    }

    public function setInputHandler(callable $handler): void
    {
        $this->onInput = \Closure::fromCallable($handler);
    }

    public function setWriteHandler(callable $handler): void
    {
        $this->onWrite = \Closure::fromCallable($handler);
    }

    public function setErrorHandler(callable $handler): void
    {
        $this->onError = \Closure::fromCallable($handler);
    }

    public function setCloseHandler(callable $handler): void
    {
        $this->onClose = \Closure::fromCallable($handler);
    }

    private function decode(string $data): MSG
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

    /**
     * Dispatch connection sent packets for check timeout, resolve deferred and send PING
     */
    public function tick(): void
    {
        foreach ($this->waitQueue as $deferred) {
            if ($deferred->getExpiredAt() < time()) {
                $deferred->failure($exception = new TimeoutException());
                call_user_func($this->onError, $exception);
                $this->close();
                return;
            }
        }

        $overdue = time() - $this->lastMessageTime > $this->inactiveTimeout;
        if ($overdue) {
            $this->send(new CMD('PING', [], null, /*TODO session prefix*/));
            $this->wait('PONG')
                ->onSuccess(fn() => null)//TODO check if error received
                ->onFailure(fn() => $this->close());
        }
    }

    public function wait(string $code): Deferred
    {
        return $this->waitQueue[] = new Deferred($code, $this->responseTimeout);
    }

    public function send(MSG $msg, bool $close = false): void
    {
        call_user_func($this->onWrite, $msg);
        $this->socket->write($msg->toString(), $close);
    }

    public function close(string $message = null): void
    {
        call_user_func($this->onClose, $message);

        $this->socket->setCloseHandler(fn() => null);
        $this->socket->close();

        $this->onInput = fn() => null;
        $this->onError = fn() => null;
        $this->onClose = fn() => null;
    }
}
