<?php

namespace PE\Component\IRC\Protocol;

final class ClientConfig
{
    public const TYPE_CLIENT  = 'client';
    public const TYPE_SERVICE = 'service';

    public string $type;
    public string $password;
    public string $nickname;

    public string $realname;
    public string $username;
    public int $usermode;

    public string $servers;
    public string $info;
}
