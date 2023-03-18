<?php

namespace PE\Component\IRC;

class Channel
{
    private string $name;
    private ?string $pass;

    public function __construct(string $name, ?string $pass = null)
    {
        $this->name = $name;
        $this->pass = $pass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function getUsers(): array
    {
        return [];//TODO
    }

    public function getFlags(): string
    {
        return '';//TODO
    }

    public function getTopic(): string
    {
        return '';//TODO
    }
}