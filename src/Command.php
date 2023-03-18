<?php

namespace PE\Component\IRC;

class Command
{
    private ?string $prefix;
    private string  $name;
    private array   $params;
    private ?string $comment;

    public function __construct(?string $prefix, string $name, array $params = [], ?string $comment = null)
    {
        $this->prefix  = $prefix;
        $this->name    = is_numeric($name) ? sprintf('%03d', $name) : $name;
        $this->params  = $params;
        $this->comment = $comment;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function __toString()
    {
        $parts = [];
        if ($this->prefix) {
            $parts[] = ':' . $this->prefix;
        }
        array_push($parts, $this->name, ...$this->params);
        if ($this->comment) {
            $parts[] = ':' . $this->comment;
        }
        return implode(' ', $parts);
    }
}