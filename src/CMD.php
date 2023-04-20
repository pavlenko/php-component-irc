<?php

namespace PE\Component\IRC;

final class CMD extends MSG
{
    public const CAP         = 'CAP';
    public const ADMIN       = 'ADMIN';//ADMIN [<server>]
    public const AWAY        = 'AWAY';
    public const CONNECT     = 'CONNECT';

    /**
     * @example CMD(ERROR :Closing Link: 127.0.0.1 (Connection timed out))
     * @example CMD(ERROR :Closing Link: 127.0.0.1 (Non-TS server)) & close immediately
     */
    public const ERROR = 'ERROR';

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

    /**
     * <code>
     * client: CMD(NICK $nickname)
     * server: CMD(NICK $nickname $hop_count $username $host $server_token $u_mode $realname)
     * </code>
     * @see ERR::NO_NICKNAME_GIVEN
     * @see ERR::ERRONEOUS_NICKNAME
     * @see ERR::NICKNAME_IN_USE
     * @see ERR::NICKNAME_COLLISION
     * @see ERR::UNAVAILABLE_RESOURCE
     * @see ERR::RESTRICTED
     */
    public const NICK = 'NICK';

    public const NOTICE      = 'NOTICE';
    public const OPERATOR    = 'OPER';
    public const PART        = 'PART';

    /**
     * <code>
     * client: CMD(PASS $password)
     * server: CMD(PASS $password $version $flags [$options])
     * </code>
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::ALREADY_REGISTERED
     */
    public const PASSWORD = 'PASS';

    public const PING        = 'PING';//PING <server> [...<server>]
    public const PONG        = 'PONG';//PONG <server> [...<server>]
    public const PRIVATE_MSG = 'PRIVMSG';
    public const QUIT        = 'QUIT';//QUIT [<quit message>]
    public const REHASH      = 'REHASH';
    public const RESTART     = 'RESTART';

    /**
     * <code>
     * CMD(SERVER $servername> $hop_count $token :$info)
     * </code>
     * @see ERR::ALREADY_REGISTERED
     */
    public const SERVER = 'SERVER';

    public const SERVER_QUIT = 'SQUIT';

    /**
     * <code>
     * client: CMD(SERVICE $nickname $_reserved $distribution $type $_reserved :$info)
     * server: CMD(SERVICE $service_name $server_token $distribution $type $hop_count :$info)
     * </code>
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::ALREADY_REGISTERED
     * @see ERR::ERRONEOUS_NICKNAME
     * @see RPL::YOU_ARE_SERVICE
     * @see RPL::YOUR_HOST
     * @see RPL::MY_INFO
     */
    public const SERVICE = 'SERVICE';

    public const STATS       = 'STATS';
    public const SUMMON      = 'SUMMON';
    public const TIME        = 'TIME';
    public const TOPIC       = 'TOPIC';
    public const TRACE       = 'TRACE';
    public const USER_HOST   = 'USERHOST';//USERHOST <nickname> [...<nickname>]

    /**
     * <code>
     * client: CMD(USER $user $mode $unused :$realname)
     * server: CMD(USER $user $mode $servername :$realname)
     * </code>
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::ALREADY_REGISTERED
     * @see ERR::NO_NICKNAME_GIVEN
     * @see ERR::PASSWORD_MISMATCH
     * @see RPL::WELCOME
     * @see RPL::YOUR_HOST
     * @see RPL::CREATED
     * @see RPL::MY_INFO
     * @see RPL::I_SUPPORT
     */
    public const USER = 'USER';

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
