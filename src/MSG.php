<?php

namespace PE\Component\IRC;

abstract class MSG
{
    private string  $code;
    private array   $args;
    private ?string $comment;
    private ?string $prefix;

    public function __construct(string $code, array $args = [], string $comment = null, string $prefix = null)
    {
        $this->code    = is_numeric($code) ? sprintf('%03d', $code) : $code;
        $this->args    = $args;
        $this->comment = null !== $comment ? $comment : $this->resolveComment();
        $this->prefix  = $prefix;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function numArgs(): int
    {
        return count($this->args);
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getArg(int $index)
    {
        return $this->args[$index] ?? null;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function toString(): string
    {
        $parts = [];

        if (!empty($this->prefix)) {
            $parts[] = ':' . $this->prefix;
        }

        array_push($parts, $this->code, ...array_filter($this->args));

        if (null !== $this->comment) {
            $parts[] = ':' . $this->comment;
        }

        return trim(implode(' ', $parts));
    }

    public function toLogger(): string
    {
        return (new \ReflectionObject($this))->getShortName() . '(' . $this->toString() . ')';
    }

    abstract protected function resolveComment(): ?string;
}
