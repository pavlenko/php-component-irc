<?php

namespace PE\Component\IRC\Client;

final class ClientConfig
{
    public const TYPE_USER    = 'user';
    public const TYPE_SERVICE = 'service';

    public const TYPES = [self::TYPE_USER, self::TYPE_SERVICE];

    public string $type;
    public ?string $password;
    public string $nickname;

    public string $realname;
    public string $username;
    public int $usermode;

    public string $servers;
    public string $info;

    private function __construct()
    {
    }

    public static function forUser(?string $password, string $nick, string $user, int $mode, string $name): self
    {
        $config = new self();
        $config->type     = self::TYPE_USER;
        $config->password = $password;
        $config->nickname = $nick;
        $config->username = $user;
        $config->usermode = $mode;
        $config->realname = $name;
        return $config;
    }

    public static function forService(?string $password, string $nick, string $servers, string $info): self
    {
        $config = new self();
        $config->type     = self::TYPE_SERVICE;
        $config->password = $password;
        $config->nickname = $nick;
        $config->servers  = $servers;
        $config->info     = $info;
        return $config;
    }
}
