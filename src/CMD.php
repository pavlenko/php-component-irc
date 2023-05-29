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

    /**
     * <code>
     * CMD(:$prefix JOIN $channels $keys)
     * CMD(:$prefix JOIN 0) - this special case for leave all channels
     * </code>
     * - $channels - comma separated channel names
     * - $keys - associated keys for listed channels
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::INVITE_ONLY_CHANNEL
     * @see ERR::CHANNEL_IS_FULL
     * @see ERR::NO_SUCH_CHANNEL
     * @see ERR::TOO_MANY_TARGETS
     * @see ERR::BANNED_FROM_CHANNEL
     * @see ERR::BAD_CHANNEL_KEY
     * @see ERR::BAD_CHANNEL_MASK
     * @see ERR::TOO_MANY_CHANNELS
     * @see ERR::UNAVAILABLE_RESOURCE
     * @see RPL::TOPIC
     */
    public const JOIN = 'JOIN';

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
     * server: CMD(NICK $nickname $hop_count $username $hostname $server_token $usermode :$realname)
     * </code>
     * - $nickname - nickname of user
     * - $hop_count - how far servers
     * - $username - username from client's user command received
     * - $hostname - hostname on which user is registered
     * - $server_token - numeric server token(id)
     * - $usermode - user mode from client's user command received
     * - $realname - real name from client's user command received
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

    /**
     * <code>
     * CMD(:$prefix PART $channels [:$part_message])
     * </code>
     * - $channels - comma separated channel names
     * - $part_message - optional leave channel message
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::NO_SUCH_CHANNEL
     * @see ERR::NOT_ON_CHANNEL
     */
    public const PART = 'PART';

    /**
     * <code>
     * client: CMD(PASS $password)
     * server: CMD(PASS $password $version $flags [:$options])
     * </code>
     * - $password - sets a connection password, must be before NICK+USER or SERVICE command is send
     * - $version - if attempt to register as a server only, protocol + software versions combination string
     * - $flags - if attempt to register as a server only, protocol implementation dependent
     * - $options - if attempt to register as a server only, connection options
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
     * CMD(SERVER $servername $hop_count $token :$info)
     * </code>
     * @see ERR::ALREADY_REGISTERED
     */
    public const SERVER = 'SERVER';

    public const SERVER_QUIT = 'SQUIT';

    /**
     * <code>
     * CMD(SERVICE $name $server_id $distribution $type $hop_count :$info)
     * </code>
     * - $name - name of service
     * - $server_id - identify server, used only for forward command to other server, else must be 0
     * - $distribution - mask for match server host
     * - $type - for now unused and must be 0
     * - $hop_count - used only for forward command to other server, else must be 0
     * - $info - service short description
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
     * client: CMD(USER $username $usermode * :$realname)
     * server: CMD(USER $username $usermode $servername :$realname)
     * </code>
     * - $username - user display name
     * - $usermode - user mode flags, bitmask, if set bit 2 - auto add mode "w", if set bit 3 - auto add mode "i"
     * - $servername - name of server on which user is registered, used only for send data to other servers, else "*"
     * - $realname - real name of user, may be first name + last name string
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
