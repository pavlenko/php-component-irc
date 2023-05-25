<?php

namespace PE\Component\IRC;

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

    public function success(RPL $rpl): void
    {
        call_user_func($this->successHandler, $rpl);
        $this->successHandler = fn() => null;
    }

    public function failure(ERR $err): void
    {
        call_user_func($this->failureHandler, $err);
        $this->failureHandler = fn() => null;
    }
}
