<?php

namespace PE\Component\IRC;

final class Channel implements ChannelInterface
{
    private SessionInterface $creator;
    private string $name;
    private string $pass;
    private string $topic = '';
    private int $limit = 0;
    private int $flags = 0;

    /** @var string[] */
    private array $banMasks = [];

    private SessionMap $sessions;
    private SessionMap $speakers;
    private SessionMap $operators;
    private SessionMap $invited;

    public function __construct(SessionInterface $creator, string $name, string $pass = null)
    {
        $this->creator = $creator;
        $this->name    = $name;
        $this->pass    = (string) $pass;

        $this->sessions  = new SessionMap();
        $this->speakers  = new SessionMap();
        $this->operators = new SessionMap();
        $this->invited   = new SessionMap();

        $this->sessions->attach($creator);
        $this->operators->attach($creator);

        $this->setFlag(self::FLAG_NO_MSG_OUT);

        //TODO sendInfo()
    }

    public function addBanMask(string $mask): void
    {
        $this->banMasks[$mask] = $mask;
    }

    public function delBanMask(string $mask): void
    {
        unset($this->banMasks[$mask]);
    }

    public function sessions(): SessionMap
    {
        return $this->sessions;
    }

    public function speakers(): SessionMap
    {
        return $this->speakers;
    }

    public function operators(): SessionMap
    {
        return $this->operators;
    }

    public function invited(): SessionMap
    {
        return $this->invited;
    }

    public function getCreator(): SessionInterface
    {
        return $this->creator;
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

    public function getNamesAsString(): string
    {
        $names = [];
        foreach ($this->sessions as $s) {
            if ($this->operators->containsName($s->getNickname())) {
                $flag = '@';
            } elseif ($this->operators->containsName($s->getNickname())) {
                $flag = '+';
            }
            $names[] = ($flag ?? '') . $s->getNickname();
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