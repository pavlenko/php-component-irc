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
        $this->items[spl_object_hash($chan)] = $chan;
    }

    public function detach(Channel $chan): void
    {
        unset($this->items[spl_object_hash($chan)]);
    }

    public function searchByName(string $name): ?ChannelInterface
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }
        return null;
    }

    public function containsName(string $name): bool
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return true;
            }
        }
        return false;
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