<?php

namespace PE\Component\IRC;

interface StorageInterface
{
    /**
     * Get config option
     *
     * @param string $name
     * @return string|int|array|null
     */
    public function conf(string $name);

    public function history(): History;

    public function channels(): ChannelMap;

    public function sessions(): SessionMap;

    public function getStartedAt(): \DateTimeInterface;

    public function isValidChannelName(string $name): bool;

    public function isValidSessionName(string $name): bool;

    public function isEqualToRegex(string $pattern, string $subject): bool;

    public function trigger(string $name, ...$args): int;
}
