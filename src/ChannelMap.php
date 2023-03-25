<?php

namespace PE\Component\IRC;

final class ChannelMap
{
    /**
     * @var array<string, Channel>
     */
    private array $items = [];

    public function attach(Channel $chan): void
    {
        $this->items[$chan->getName()] = $chan;
    }

    public function detach(Channel $chan): void
    {
        unset($this->items[$chan->getName()]);
    }

    public function containsName(string $name): bool
    {
        return isset($this->items[$name]);
    }

    public function current(): Channel
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): string
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }
}