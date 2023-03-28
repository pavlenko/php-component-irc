<?php

namespace PE\Component\IRC;

final class CMD extends MSG
{
    public const CMD_CAP         = 'CAP';
    public const CMD_ADMIN       = 'ADMIN';//ADMIN [<server>]
    public const CMD_AWAY        = 'AWAY';
    public const CMD_CONNECT     = 'CONNECT';
    public const CMD_ERROR       = 'ERROR';
    public const CMD_INFO        = 'INFO';
    public const CMD_INVITE      = 'INVITE';
    public const CMD_IS_ON       = 'ISON';//ISON <nickname> [...<nickname>]
    public const CMD_JOIN        = 'JOIN';
    public const CMD_KICK        = 'KICK';
    public const CMD_KILL        = 'KILL';
    public const CMD_LINKS       = 'LINKS';
    public const CMD_LIST        = 'LIST';
    public const CMD_MODE        = 'MODE';
    public const CMD_MOTD        = 'MOTD';//MODT [<server>]
    public const CMD_LIST_USERS  = 'LUSERS';//LUSERS [<mask> [<target>]]
    public const CMD_NAMES       = 'NAMES';
    public const CMD_NICK        = 'NICK';
    public const CMD_NOTICE      = 'NOTICE';
    public const CMD_OPERATOR    = 'OPER';
    public const CMD_PART        = 'PART';
    public const CMD_PASSWORD    = 'PASS';
    public const CMD_PING        = 'PING';//PING <server> [...<server>]
    public const CMD_PONG        = 'PONG';//PONG <server> [...<server>]
    public const CMD_PRIVATE_MSG = 'PRIVMSG';
    public const CMD_QUIT        = 'QUIT';//QUIT [<quit message>]
    public const CMD_REHASH      = 'REHASH';
    public const CMD_RESTART     = 'RESTART';
    public const CMD_SERVER      = 'SERVER';
    public const CMD_SERVER_QUIT = 'SQUIT';
    public const CMD_STATS       = 'STATS';
    public const CMD_SUMMON      = 'SUMMON';
    public const CMD_TIME        = 'TIME';
    public const CMD_TOPIC       = 'TOPIC';
    public const CMD_TRACE       = 'TRACE';
    public const CMD_USER_HOST   = 'USERHOST';//USERHOST <nickname> [...<nickname>]
    public const CMD_USER        = 'USER';
    public const CMD_USERS       = 'USERS';
    public const CMD_VERSION     = 'VERSION';//VERSION [<server>]
    public const CMD_WALLOPS     = 'WALLOPS';//WALLOPS <text>
    public const CMD_WHOIS       = 'WHOIS';
    public const CMD_WHO         = 'WHO';
    public const CMD_WHO_WAS     = 'WHOWAS';

    protected function resolveComment(): ?string
    {
        return null;// Do nothing for now
    }
}
