<?php

namespace PE\Component\IRC\Exception;

final class DeferredException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var mixed
     */
    private $reason;

    /**
     * Special exception for deferred errors
     *
     * @param mixed $reason
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($reason, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->reason = $reason;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }
}
