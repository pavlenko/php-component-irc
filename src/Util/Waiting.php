<?php

namespace PE\Component\IRC\Util;

/**
 * Class represent waiting for some response code(s)
 */
final class Waiting
{
    private ?Deferred2 $deferred = null;

    private int $expiredAt;
    private array $expectCodes;

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

    public function deferred(): Deferred2
    {
        if ($this->deferred === null) {
            $this->deferred = null;
        }

        return $this->deferred;
    }
}
