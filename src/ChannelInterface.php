<?php

namespace PE\Component\IRC;

interface ChannelInterface
{
    // Modify "o","l","b","v","k" flags has separate logic
    public const FLAG_PRIVATE     = 0b000001;//p - private channel
    public const FLAG_SECRET      = 0b000010;//s - secret channel
    public const FLAG_MODERATED   = 0b000100;//m - moderated channel
    public const FLAG_INVITE_ONLY = 0b001000;//i - invite only channel
    public const FLAG_TOPIC_SET   = 0b010000;//t - topic settable by channel operator only
    public const FLAG_NO_MSG_OUT  = 0b100000;//n - no messages to channel from clients on the outside

    public function __construct(string $name, string $pass = null);

    public function getBanMasks(): array;
    public function addBanMask(string $mask): void;
    public function delBanMask(string $mask): void;

    public function numSessions(): int;

    /**
     * @param StorageInterface $storage
     * @return SessionInterface[]
     */
    public function getSessions(StorageInterface $storage): array;
    public function hasSession(SessionInterface $session): bool;
    public function addSession(SessionInterface $session): void;
    public function delSession(SessionInterface $session): void;

    public function numSpeakers(): int;

    /**
     * @param StorageInterface $storage
     * @return SessionInterface[]
     */
    public function getSpeakers(StorageInterface $storage): array;
    public function hasSpeaker(SessionInterface $session): bool;
    public function addSpeaker(SessionInterface $session): void;
    public function delSpeaker(SessionInterface $session): void;

    public function numOperators(): int;

    /**
     * @param StorageInterface $storage
     * @return SessionInterface[]
     */
    public function getOperators(StorageInterface $storage): array;
    public function hasOperator(SessionInterface $session): bool;
    public function addOperator(SessionInterface $session): void;
    public function delOperator(SessionInterface $session): void;

    public function numInvited(): int;

    /**
     * @param StorageInterface $storage
     * @return SessionInterface[]
     */
    public function getInvited(StorageInterface $storage): array;
    public function hasInvited(SessionInterface $session): bool;
    public function addInvited(SessionInterface $session): void;
    public function delInvited(SessionInterface $session): void;

    public function getName(): string;
    public function getPass(): string;
    public function setPass(string $pass): void;
    public function getTopic(): string;
    public function setTopic(string $topic): void;
    public function getLimit(): int;
    public function setLimit(int $limit): void;
    public function getNamesAsString(StorageInterface $storage): string;
    public function getFlagsAsString(): string;
    public function hasFlag(int $flag): bool;
    public function setFlag(int $flag): void;
    public function clrFlag(int $flag): void;
}
