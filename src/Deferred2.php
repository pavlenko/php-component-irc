<?php

namespace PE\Component\IRC;

//TODO write example code usages for multi-response commands
use PE\Component\Socket\SelectInterface;

class Deferred2
{
    private int $expiredAt;
    private array $expectCodes;

    private ?\Closure $onSuccess = null;
    private ?\Closure $onFailure = null;

    /**
     * @var null|\Throwable|mixed
     */
    private $value = null;

    public function __construct(int $timeout, string ...$expectCode)
    {
        $this->expiredAt   = time() + $timeout;
        $this->expectCodes = $expectCode;
    }

    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    public function isExpectCode(string $code): bool
    {
        return in_array($code, $this->expectCodes);
    }

    public function then(callable $handler)
    {}

    public function else(callable $handler)
    {}

    public function resolve($value): void
    {
        $this->value  = $value;
        if ($value instanceof \Throwable) {
            if (null !== $this->onFailure) {
                call_user_func($this->onFailure, $value);
            } else {
                throw $value;
            }
        } else {
            if (null !== $this->onSuccess) {
                call_user_func($this->onSuccess, $value);
            }
        }
    }

    public function wait(SelectInterface $select)
    {
        while (null === $this->value) {
            $select->dispatch();
            usleep(1000);
        }

        return $this->value;
    }
}
