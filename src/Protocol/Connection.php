<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\Exception\TimeoutException;
use PE\Component\IRC\MSG;

final class Connection
{
    private \PE\Component\Socket\Client $socket;
    private int $responseTimeout;
    private int $inactiveTimeout;
    private int $lastMessageTime = 0;

    private \Closure $onInput;
    private \Closure $onError;
    private \Closure $onClose;
    private array $waitQueue = [];

    public function __construct(
        \PE\Component\Socket\Client $socket,
        int $responseTimeout = Config::DEFAULT_RESPONSE_TIMEOUT,
        int $inactiveTimeout = Config::DEFAULT_INACTIVE_TIMEOUT
    ) {
        $this->socket          = $socket;
        $this->responseTimeout = $responseTimeout;
        $this->inactiveTimeout = $inactiveTimeout;

        $this->onInput = fn() => null;
        $this->onError = fn() => null;
        $this->onClose = fn() => null;
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
            $this->send(new CMD('PING'));
            $this->wait('PONG')->onFailure(fn() => $this->close());
        }
    }

    public function wait(string $code): Deferred
    {
        return $this->waitQueue[] = new Deferred($code, $this->responseTimeout);
    }

    public function send(MSG $msg, bool $close = false): void
    {
        $this->socket->write($msg->toString(), $close);
    }

    public function close(): void
    {
        $this->socket->close();
    }
}
