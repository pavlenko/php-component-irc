<?php

namespace PE\Component\IRC;

interface ChannelInterface
{
    public function __construct(SessionInterface $creator, string $name, string $pass = '');

    public function sessions(): SessionMap;
    public function speakers(): SessionMap;
    public function operators(): SessionMap;
    public function invited(): SessionMap;

    public function getCreator(): SessionInterface;
    public function getName(): string;
    public function getPass(): string;
    public function setPass(string $pass): void;
    public function getTopic(): string;
    public function setTopic(string $topic): void;
    public function getLimit(): int;
    public function setLimit(int $limit): void;
    public function getFlagsAsString(): string;
    public function hasFlag(int $flag): bool;
    public function setFlag(int $flag): void;
    public function clrFlag(int $flag): void;
}