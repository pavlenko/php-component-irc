<?php

namespace PE\Component\IRC\Util;

use PE\Component\Socket\SelectInterface;

/**
 * Class for store callbacks for delayed execution
 */
final class Deferred2
{
    private ?\Closure $onSuccess = null;
    private ?\Closure $onFailure = null;

    /**
     * @var null|\Throwable|mixed
     */
    private $value = null;

    public function __construct()
    {
    }

    public function then(callable $handler)
    {
        $this->onSuccess = \Closure::fromCallable($handler);
    }

    public function else(callable $handler)
    {
        $this->onSuccess = \Closure::fromCallable($handler);
    }

    //TODO check how to prevent exceptions in internal usage
    public function resolve($value): void
    {
        $this->value  = $value;
        if ($value instanceof \Throwable) {
            if (null !== $this->onFailure) {
                call_user_func($this->onFailure, $value);
            } else {
                throw $value;
            }
        } elseif (null !== $this->onSuccess) {
            call_user_func($this->onSuccess, $value);
        }
    }

    // This allows block execute code after until value is resolved
    public function wait(SelectInterface $select)
    {
        while (null === $this->value) {
            $select->dispatch();
            usleep(1000);
        }

        return $this->value;
    }
}
