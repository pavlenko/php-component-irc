<?php

namespace PE\Component\IRC;

class Command
{
    private string  $name;
    private array   $params;
    private ?string $comment;
    private ?string $prefix;

    public function __construct(string $name, array $params = [], ?string $comment = null, ?string $prefix = null)
    {
        $this->name    = is_numeric($name) ? sprintf('%03d', $name) : $name;
        $this->params  = $params;
        $this->comment = $comment;
        $this->prefix  = $prefix;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function getParam(int $index)
    {
        return $this->params[$index] ?? null;
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
        array_push($parts, $this->name, ...$this->params);
        if ($this->comment) {
            $parts[] = ':' . $this->comment;
        }
        return implode(' ', $parts);
    }
}