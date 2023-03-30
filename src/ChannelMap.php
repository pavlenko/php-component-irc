<?php

namespace PE\Component\IRC;

final class ChannelMap implements \Countable, \Iterator
{
    /**
     * @var array<string, ChannelInterface>
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

    public function searchByName(string $name): ?ChannelInterface
    {
        return $this->items[$name] ?? null;
    }

    public function containsName(string $name): bool
    {
        return isset($this->items[$name]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): ?Channel
    {
        return current($this->items) ?: null;
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