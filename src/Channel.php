<?php

namespace PE\Component\IRC;

final class Channel implements ChannelInterface
{
    private string $name;
    private string $pass;
    private string $topic = '';
    private int $limit = 0;
    private int $flags = 0;

    /** @var string[] */
    private array $banMasks = [];

    private array $sessions = [];
    private array $speakers = [];
    private array $operators = [];
    private SessionMap $invited;

    public function __construct(string $name, string $pass = null)
    {
        $this->name = $name;
        $this->pass = (string) $pass;

        $this->invited   = new SessionMap();

        $this->setFlag(self::FLAG_NO_MSG_OUT);
    }

    public function getBanMasks(): array
    {
        return $this->banMasks;
    }

    public function addBanMask(string $mask): void
    {
        $this->banMasks[$mask] = $mask;
    }

    public function delBanMask(string $mask): void
    {
        unset($this->banMasks[$mask]);
    }

    public function numSessions(): int
    {
        return count($this->sessions);
    }

    public function getSessions(StorageInterface $storage): array
    {
        return array_filter(iterator_to_array($storage->sessions()), fn($i) => $this->hasSession($i));
    }

    public function hasSession(SessionInterface $session): bool
    {
        return array_key_exists(spl_object_hash($session), $this->sessions);
    }

    public function addSession(SessionInterface $session): void
    {
        $this->sessions[spl_object_hash($session)] = spl_object_id($session);
    }

    public function delSession(SessionInterface $session): void
    {
        //TODO detach from all
        unset($this->sessions[spl_object_hash($session)]);
    }

    public function numSpeakers(): int
    {
        return count($this->speakers);
    }

    public function getSpeakers(StorageInterface $storage): array
    {
        return array_filter(iterator_to_array($storage->sessions()), fn($i) => $this->hasSpeaker($i));
    }

    public function hasSpeaker(SessionInterface $session): bool
    {
        return array_key_exists(spl_object_hash($session), $this->speakers);
    }

    public function addSpeaker(SessionInterface $session): void
    {
        $this->speakers[spl_object_hash($session)] = spl_object_id($session);
    }

    public function delSpeaker(SessionInterface $session): void
    {
        unset($this->speakers[spl_object_hash($session)]);
    }

    public function numOperators(): int
    {
        return count($this->operators);
    }

    public function getOperators(StorageInterface $storage): array
    {
        return array_filter(iterator_to_array($storage->sessions()), fn($i) => $this->hasOperator($i));
    }

    public function hasOperator(SessionInterface $session): bool
    {
        return array_key_exists(spl_object_hash($session), $this->operators);
    }

    public function addOperator(SessionInterface $session): void
    {
        $this->operators[spl_object_hash($session)] = spl_object_id($session);
    }

    public function delOperator(SessionInterface $session): void
    {
        unset($this->operators[spl_object_hash($session)]);
    }

    public function invited(): SessionMap
    {
        return $this->invited;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getNamesAsString(StorageInterface $storage): string
    {
        $names = [];
        foreach ($this->getSessions($storage) as $user) {
            if ($this->hasOperator($user)) {
                $flag = '@';
            } elseif ($this->hasSpeaker($user)) {
                $flag = '+';
            }
            $names[] = ($flag ?? '') . $user->getNickname();
        }
        return implode(' ', $names);
    }

    public function getFlagsAsString(): string
    {
        $flags = '+';
        if ($this->hasFlag(self::FLAG_INVITE_ONLY)) {
            $flags .= 'i';
        }
        if ($this->hasFlag(self::FLAG_NO_MSG_OUT)) {
            $flags .= 'n';
        }
        if ($this->hasFlag(self::FLAG_PRIVATE)) {
            $flags .= 'p';
        }
        if ($this->hasFlag(self::FLAG_SECRET)) {
            $flags .= 's';
        }
        if ($this->hasFlag(self::FLAG_TOPIC_SET)) {
            $flags .= 't';
        }
        return $flags;
    }

    public function hasFlag(int $flag): bool
    {
        return $this->flags & $flag;
    }

    public function setFlag(int $flag): void
    {
        $this->flags |= $flag;
    }

    public function clrFlag(int $flag): void
    {
        $this->flags &= ~$flag;
    }
}