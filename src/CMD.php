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

    /**
     * <code>
     * CMD(:$prefix INVITE $nickname $channel)
     * </code>
     * - $nickname - user to invite
     * - $channel - channel to invite to
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::NOTONCHANNEL
     * @see ERR::CHANOPRIVSNEEDED
     * @see ERR::NOSUCHNICK
     * @see ERR::USERONCHANNEL
     * @see RPL::INVITING
     * @see RPL::AWAY
     */
    public const INVITE = 'INVITE';

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
     * @see RPL::NAMES_REPLY
     * @see RPL::END_OF_NAMES
     */
    public const JOIN = 'JOIN';

    /**
     * <code>
     * CMD(KICK $channels $users)
     * </code>
     * - $channels - comma separated list of channel to kick of
     * - $users - comma separated list of users to kick off channels
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::BAD_CHANNEL_MASK
     * @see ERR::USER_NOT_IN_CHANNEL
     * @see ERR::NO_SUCH_CHANNEL
     * @see ERR::OPERATOR_PRIVILEGES_NEEDED
     * @see ERR::NOT_ON_CHANNEL
     */
    public const KICK = 'KICK';

    public const KILL        = 'KILL';
    public const LINKS       = 'LINKS';

    /**
     * <code>
     * CMD(LIST $channels [$target])
     * </code>
     * - $channels - channels to limit list to
     * - $target - target server(s) from which get list
     * @see ERR::TOO_MANY_MATCHES
     * @see ERR::NO_SUCH_SERVER
     * @see RPL::LIST
     * @see RPL::LIST_END
     */
    public const LIST = 'LIST';

    /**
     * <code>
     * channel: CMD(MODE $channel *($mode [$mode_params]))
     * </code>
     * - $channel - channel for get/set mode
     * - $mode - modes to change/get
     * - $mode_params - params for modes
     *
     * combination of "$mode [$mode_params]" can be repeated
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::NO_CHANNEL_MODES
     * @see ERR::USER_NOT_IN_CHANNEL
     * @see ERR::KEY_SET
     * @see ERR::OPERATOR_PRIVILEGES_NEEDED
     * @see ERR::UNKNOWN_MODE
     * @see RPL::CHANNEL_MODE_IS
     * @see RPL::BAN_LIST
     * @see RPL::EXCEPTION_LIST
     * @see RPL::INVITE_LIST
     * @see RPL::UNIQUE_OPERATOR_IS - !!! no documentation for this
     * @see RPL::END_OF_BAN_LIST
     * @see RPL::END_OF_EXCEPTION_LIST
     * @see RPL::END_OF_INVITE_LIST
     */
    public const MODE = 'MODE';

    public const MOTD        = 'MOTD';//MODT [<server>]
    public const LIST_USERS  = 'LUSERS';//LUSERS [<mask> [<target>]]

    /**
     * <code>
     * CMD(NAMES $channels, [:$target])
     * </code>
     * - $channels - comma separated channels list
     * - $target - target server(s) from which get list
     * @see ERR::TOO_MANY_TARGETS
     * @see ERR::NO_SUCH_SERVER
     * @see RPL::NAMES_REPLY
     * @see RPL::END_OF_NAMES
     */
    public const NAMES = 'NAMES';

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

    /**
     * <code>
     * CMD(TOPIC $channel :$topic)
     * </code>
     * - $channel - channel to view or change topic
     * - $topic - topic to set
     * @see ERR::NEED_MORE_PARAMS
     * @see ERR::NOT_ON_CHANNEL
     * @see ERR::OPERATOR_PRIVILEGES_NEEDED
     * @see ERR::NO_CHANNEL_MODES
     * @see RPL::NO_TOPIC
     * @see RPL::TOPIC
     */
    public const TOPIC = 'TOPIC';

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
