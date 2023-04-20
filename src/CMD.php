<?php

namespace PE\Component\IRC;

final class CMD extends MSG
{
    public const CAP         = 'CAP';
    public const ADMIN       = 'ADMIN';//ADMIN [<server>]
    public const AWAY        = 'AWAY';
    public const CONNECT     = 'CONNECT';
    public const ERROR       = 'ERROR';
    public const INFO        = 'INFO';
    public const INVITE      = 'INVITE';
    public const IS_ON       = 'ISON';//ISON <nickname> [...<nickname>]
    public const JOIN        = 'JOIN';
    public const KICK        = 'KICK';
    public const KILL        = 'KILL';
    public const LINKS       = 'LINKS';
    public const LIST        = 'LIST';//LIST [<channel>{,<channel>} [<server>]]
    public const MODE        = 'MODE';
    public const MOTD        = 'MOTD';//MODT [<server>]
    public const LIST_USERS  = 'LUSERS';//LUSERS [<mask> [<target>]]
    public const NAMES       = 'NAMES';
    public const NICK        = 'NICK';
    public const NOTICE      = 'NOTICE';
    public const OPERATOR    = 'OPER';
    public const PART        = 'PART';
    public const PASSWORD    = 'PASS';
    public const PING        = 'PING';//PING <server> [...<server>]
    public const PONG        = 'PONG';//PONG <server> [...<server>]
    public const PRIVATE_MSG = 'PRIVMSG';
    public const QUIT        = 'QUIT';//QUIT [<quit message>]
    public const REHASH      = 'REHASH';
    public const RESTART     = 'RESTART';
    public const SERVER      = 'SERVER';
    public const SERVER_QUIT = 'SQUIT';
    public const STATS       = 'STATS';
    public const SUMMON      = 'SUMMON';
    public const TIME        = 'TIME';
    public const TOPIC       = 'TOPIC';
    public const TRACE       = 'TRACE';
    public const USER_HOST   = 'USERHOST';//USERHOST <nickname> [...<nickname>]
    public const USER        = 'USER';
    public const USERS       = 'USERS';
    public const VERSION     = 'VERSION';//VERSION [<server>]
    public const WALLOPS     = 'WALLOPS';//WALLOPS <text>
    public const WHOIS       = 'WHOIS';
    public const WHO         = 'WHO';
    public const WHO_WAS     = 'WHOWAS';

    protected function resolveComment(): ?string
    {
        return null;// Do nothing for now
    }
}
