<?php

namespace PE\Component\IRC;

final class SessionMap implements \Iterator
{
    /**
     * @var array<string, SessionInterface>
     */
    private array $items = [];

    public function attach(SessionInterface $sess): void
    {
        $this->items[spl_object_hash($sess)] = $sess;
    }

    public function detach(SessionInterface $sess): void
    {
        unset($this->items[spl_object_hash($sess)]);
    }

    public function searchByName(string $name): ?SessionInterface
    {
        foreach ($this->items as $sess) {
            if ($sess->getNickname() === $name) {
                return $sess;
            }
        }
        return null;
    }

    public function containsName(string $name): bool
    {
        foreach ($this->items as [$conn, $sess]) {
            if ($sess->getNickname() === $name) {
                return true;
            }
        }
        return false;
    }

    public function current(): ?SessionInterface
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