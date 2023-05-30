<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Config;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\ERR;
use PE\Component\IRC\Exception\TimeoutException;
use PE\Component\IRC\MSG;
use PE\Component\IRC\RPL;
use PE\Component\Socket\Client as SocketClient;

final class Connection
{
    private SocketClient $socket;
    private int $responseTimeout;
    private int $inactiveTimeout;
    private int $lastPingingTime = 0;
    private int $lastMessageTime = 0;

    private \Closure $onInput;
    private \Closure $onWrite;
    private \Closure $onError;
    private \Closure $onClose;

    private ?string $clientAddress = null;
    private ?string $remoteAddress = null;

    /**
     * @var Deferred[]
     */
    private array $waitQueue = [];
    private string $buffer = '';

    public function __construct(
        SocketClient $socket,
        int          $responseTimeout = Config::DEFAULT_RESPONSE_TIMEOUT,
        int          $inactiveTimeout = Config::DEFAULT_INACTIVE_TIMEOUT
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
                $this->processReceive($data);
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

    public function getClientAddress(): ?string
    {
        if (null === $this->clientAddress) {
            $this->clientAddress = $this->socket->getClientAddress();
        }
        return $this->clientAddress;
    }

    public function getRemoteAddress(): ?string
    {
        if (null === $this->remoteAddress) {
            $this->remoteAddress = $this->socket->getRemoteAddress();
        }
        return $this->remoteAddress;
    }

    private function processMessage(MSG $message): void
    {
        // Check wait for specific code - the resolve deferred and remove from it queue
        foreach ($this->waitQueue as $index => $deferred) {
            if ($message->getCode() === $deferred->getExpectCode()) {
                $deferred->success($message);
                unset($this->waitQueue[$index]);
                break;
            }
        }

        call_user_func($this->onInput, $message);
    }

    private function processReceive(string $data): void
    {
        $this->buffer .= $data;
        while (strlen($this->buffer) > 0 && false !== ($pos = strpos($this->buffer, "\n"))) {
            $message = $this->decode(trim(substr($this->buffer, 0, $pos)));
            $this->processMessage($message);
            $this->buffer = substr($this->buffer, $pos + 1);
        }
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

    /**
     * @param string|string[] $code
     * @return Deferred
     */
    public function wait($code): Deferred
    {
        return $this->waitQueue[] = new Deferred($code, $this->responseTimeout);
    }

    public function send(MSG $msg, bool $close = false): void
    {
        call_user_func($this->onWrite, $msg);
        $this->socket->write($msg->toString() . "\r\n", $close);
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
