<?php

namespace PE\Component\IRC\Util;

use PE\Component\IRC\Exception\DeferredException;
use PE\Component\Socket\SelectInterface;

/**
 * Class for store callbacks for delayed execution
 */
final class Deferred2
{
    public const TIMEOUT  = 30;
    public const PENDING  = 'pending';
    public const RESOLVED = 'resolved';
    public const REJECTED = 'rejected';

    private ?\Closure $onSuccess = null;
    private ?\Closure $onFailure = null;

    /**
     * @var mixed
     */
    private $result = null;
    private string $status = self::PENDING;

    public function then(callable $handler): self
    {
        if (null !== $this->onSuccess) {
            throw new \UnexpectedValueException('You cannot override onSuccess callback if it already set');
        }

        $this->onSuccess = \Closure::fromCallable($handler);
        return $this;
    }

    public function else(callable $handler): self
    {
        if (null !== $this->onFailure) {
            throw new \UnexpectedValueException('You cannot override onFailure callback if it already set');
        }

        $this->onFailure = \Closure::fromCallable($handler);
        return $this;
    }

    public function resolved($value): void
    {
        if ($this->status === self::PENDING) {
            $this->status = self::RESOLVED;
            $this->result = $value;
            if ($this->onSuccess !== null) {
                call_user_func($this->onSuccess, $value);
            }
        }
    }

    public function rejected($error): void
    {
        if ($this->status === self::PENDING) {
            $this->status = self::REJECTED;
            $this->result = $error;
            if ($this->onFailure !== null) {
                call_user_func($this->onFailure, $error);
            }
        }
    }

    /**
     * Wait for result (like JS await), return it on success or throw some exception on failure
     *
     * @param SelectInterface $select
     * @param int $timeout
     * @return mixed
     * @throws \Throwable
     */
    public function wait(SelectInterface $select, int $timeout = self::TIMEOUT)
    {
        $start = microtime(true);
        while (null === $this->result && microtime(true) - $start < $timeout) {
            $select->dispatch();
            usleep(1000);
        }

        if ($this->status === self::RESOLVED) {
            return $this->result;
        }

        if ($this->status === self::REJECTED) {
            if (!$this->result instanceof \Throwable) {
                throw new DeferredException($this->result);
            }

            throw $this->result;
        }

        throw new DeferredException(null);
    }
}
