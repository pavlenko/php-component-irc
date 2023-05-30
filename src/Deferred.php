<?php

namespace PE\Component\IRC;

//TODO rethink deferred, maybe add possible responses list (both error + reply)

//TODO all cmd, rpl, err is a success responses

/* @deprecated */
final class Deferred
{
    private string $expectCode;
    private int $expiredAt;
    private \Closure $successHandler;
    private \Closure $failureHandler;

    public function __construct(string $expectCode, int $timeout)
    {
        $this->expectCode = $expectCode;
        $this->expiredAt  = time() + $timeout;

        $this->successHandler = fn() => null;
        $this->failureHandler = fn() => null;
    }

    public function getExpectCode(): string
    {
        return $this->expectCode;
    }

    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    public function onSuccess(callable $handler): void
    {
        $this->successHandler = \Closure::fromCallable($handler);
    }

    public function onFailure(callable $handler): void
    {
        $this->failureHandler = \Closure::fromCallable($handler);
    }

    public function success(MSG $msg): void
    {
        call_user_func($this->successHandler, $msg);
    }

    public function failure(\Throwable $exception): void
    {
        call_user_func($this->failureHandler, $exception);
    }
}
