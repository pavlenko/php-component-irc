<?php

namespace PE\Component\IRC;

final class SessionMap implements \Iterator
{
    /**
     * @var array<string, array<Connection|SessionInterface>>
     */
    private array $items = [];

    public function attach(Connection $conn, SessionInterface $sess): void
    {
        $this->items[spl_object_hash($conn)] = [$conn, $sess];
    }

    public function detach(Connection $conn): void
    {
        unset($this->items[spl_object_hash($conn)]);
    }

    public function searchByName(string $name): ?array
    {
        foreach ($this->items as [$conn, $sess]) {
            if ($sess->getNickname() === $name) {
                return [$conn, $sess];
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

    /**
     * @return array<Connection|SessionInterface>
     */
    public function current(): array
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