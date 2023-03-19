<?php

namespace PE\Component\IRC;

//TODO add constants CMD_* with names of commands
//TODO move here RPL_* and ERR_* codes
class Command
{
    private string  $name;
    private array   $args;
    private ?string $comment;
    private ?string $prefix;

    public function __construct(string $name, array $args = [], ?string $comment = null, ?string $prefix = null)
    {
        $this->name    = is_numeric($name) ? sprintf('%03d', $name) : $name;
        $this->args    = $args;
        $this->comment = $comment;//TODO if null - try to resolve depends on NAME/CODE & args
        $this->prefix  = $prefix;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param int $index
     * @return mixed
     */
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

    public function __toString()
    {
        $parts = [];
        if ($this->prefix) {
            $parts[] = ':' . $this->prefix;
        }
        array_push($parts, $this->name, ...$this->args);
        if ($this->comment) {
            $parts[] = ':' . $this->comment;
        }
        return implode(' ', $parts);
    }
}