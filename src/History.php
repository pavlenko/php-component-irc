<?php

namespace PE\Component\IRC;

final class History
{
    /**
     * @var HistoryItem[]
     */
    private array $items = [];

    public function addSession(SessionInterface $sess): void
    {
        $this->items[] = new HistoryItem($sess);
    }

    public function getByName(string $name): array
    {
        $history = [];
        foreach ($this->items as $item) {
            if ($item->getNickname() === $name) {
                $history[] = $item;
            }
        }
        return $history;
    }
}