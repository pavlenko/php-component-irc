<?php

namespace PE\Component\IRC;

final class ERR extends MSG
{
    public const NO_SUCH_NICK               = '401';//:<prefix> <rpl code> <curr nick> <nickname> :No such nick/channel
    public const NO_SUCH_SERVER             = '402';//:<prefix> <rpl code> <curr nick> <server name> :No such server
    public const NO_SUCH_CHANNEL            = '403';//:<prefix> <rpl code> <curr nick> <channel name> :No such channel
    public const CANNOT_SEND_TO_CHANNEL     = '404';//:<prefix> <rpl code> <curr nick> <channel name> :Cannot send to channel
    public const TOO_MANY_CHANNELS          = '405';//:<prefix> <rpl code> <curr nick> <channel name> :You have joined too many channels
    public const WAS_NO_SUCH_NICK           = '406';//:<prefix> <rpl code> <curr nick> <nickname> :There was no such nickname
    public const TOO_MANY_TARGETS           = '407';//:<prefix> <rpl code> <curr nick> <target> :Duplicate recipients. No message delivered
    public const NO_ORIGIN                  = '409';//:<prefix> <rpl code> <curr nick> :No origin specified
    public const NO_RECIPIENT               = '411';//:<prefix> <rpl code> <curr nick> :No recipient given (<command>)
    public const NO_TEXT_TO_SEND            = '412';//:<prefix> <rpl code> <curr nick> :No text to send
    public const NO_TOP_LEVEL               = '413';//:<prefix> <rpl code> <curr nick> <mask> :No toplevel domain specified
    public const WILDCARD_TOP_LEVEL         = '414';//:<prefix> <rpl code> <curr nick> <mask> :Wildcard in toplevel domain
    // Returned by a server in response to a LIST or NAMES message to indicate the result contains too many items to be returned to the client.
    public const TOO_MANY_MATCHES           = '416';//:<prefix> <rpl code> <curr nick> <channel> :Output too long (try locally)
    public const UNKNOWN_COMMAND            = '421';//:<prefix> <rpl code> <curr nick> <command> :Unknown command
    public const NO_MOTD                    = '422';//:<prefix> <rpl code> <curr nick> :MOTD File is missing
    public const NO_ADMIN_INFO              = '423';//:<prefix> <rpl code> <curr nick> <server> :No administrative info available
    public const FILE_ERROR                 = '424';//:<prefix> <rpl code> <curr nick> :File error doing <file op> on <file>

    /**
     * <code>
     * ERR(:$prefix NO_NICKNAME_GIVEN $curr_nick :No nickname given)
     * </code>
     */
    public const NO_NICKNAME_GIVEN = '431';

    /**
     * <code>
     * ERR(:$prefix ERRONEOUS_NICKNAME $curr_nick $nick :Erroneous nickname)
     * </code>
     */
    public const ERRONEOUS_NICKNAME = '432';

    /**
     * <code>
     * ERR(:$prefix NICKNAME_IN_USE $curr_nick $nick :Nickname is already in use)
     * </code>
     */
    public const NICKNAME_IN_USE = '433';

    /**
     * <code>
     * ERR(:$prefix NICKNAME_COLLISION $curr_nick $nick :Nickname collision KILL)
     * </code>
     */
    public const NICKNAME_COLLISION = '436';

    /**
     * <code>
     * ERR(:$prefix UNAVAILABLE_RESOURCE $curr_nick $nick/$channel :Nick/channel is temporarily unavailable)
     * </code>
     */
    public const UNAVAILABLE_RESOURCE = '437';

    public const USER_NOT_IN_CHANNEL        = '441';//:<prefix> <rpl code> <curr nick> <nick> <channel> :They aren’t on that channel
    public const NOT_ON_CHANNEL             = '442';//:<prefix> <rpl code> <curr nick> <channel> :You’re not on that channel
    public const USER_ON_CHANNEL            = '443';//:<prefix> <rpl code> <curr nick> <user> <channel> :is already on channel
    public const NO_LOGIN                   = '444';//:<prefix> <rpl code> <curr nick> <user> :User not logged in
    public const SUMMON_DISABLED            = '445';//:<prefix> <rpl code> <curr nick> :SUMMON has been disabled
    public const USERS_DISABLED             = '446';//:<prefix> <rpl code> <curr nick> :USERS has been disabled
    public const NOT_REGISTERED             = '451';//:<prefix> <rpl code> <curr nick> :You have not registered

    /**
     * <code>
     * ERR(:$prefix NEED_MORE_PARAMS $curr_nick $command :Not enough parameters)
     * </code>
     */
    public const NEED_MORE_PARAMS = '461';

    /**
     * <code>
     * ERR(:$prefix ALREADY_REGISTERED $curr_nick :You may not re-register)
     * </code>
     * @see CMD::PASSWORD
     * @see CMD::SERVER
     * @see CMD::SERVICE
     * @see CMD::USER
     */
    public const ALREADY_REGISTERED = '462';

    public const NO_PERM_FOR_HOST           = '463';//:<prefix> <rpl code> <curr nick> :Your host isn’t among the privileged

    /**
     * <code>
     * ERR(:$prefix PASSWORD_MISMATCH $curr_nick :Password incorrect)
     * </code>
     * @see CMD::USER
     * @see CMD::OPERATOR
     */
    public const PASSWORD_MISMATCH = '464';

    public const YOU_ARE_BANNED_CREEP       = '465';//:<prefix> <rpl code> <curr nick> :You are banned from this server
    public const KEY_SET                    = '467';//:<prefix> <rpl code> <curr nick> <channel> :Channel key already set
    public const CHANNEL_IS_FULL            = '471';//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+l)
    public const UNKNOWN_MODE               = '472';//:<prefix> <rpl code> <curr nick> <char> :is unknown mode char to me
    public const INVITE_ONLY_CHANNEL        = '473';//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+i)
    public const BANNED_FROM_CHANNEL        = '474';//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+b)
    public const BAD_CHANNEL_KEY            = '475';//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+k)


    public const BAD_CHANNEL_MASK           = '476';//:<prefix> <rpl code> <curr nick> <channel> :Bad Channel Mask

    /**
     * <code>
     * ERR(:$prefix NO_CHANNEL_MODES $curr_nick $channel :Channel does not support modes)
     * </code>
     */
    public const NO_CHANNEL_MODES = '477';

    public const NO_PRIVILEGES              = '481';//:<prefix> <rpl code> <curr nick> :Permission Denied - You’re not an IRC operator
    public const OPERATOR_PRIVILEGES_NEEDED = '482';//:<prefix> <rpl code> <curr nick> <channel> :You’re not channel operator
    public const CANNOT_KILL_SERVER         = '483';//:<prefix> <rpl code> <curr nick> :You cant kill a server!

    /**
     * <code>
     * ERR(:$prefix RESTRICTED $curr_nick :Your connection is restricted!)
     * </code>
     * @see CMD::NICK
     */
    public const RESTRICTED = '484';

    public const NO_OPERATOR_HOST           = '491';//:<prefix> <rpl code> <curr nick> :No O-lines for your host
    public const USER_MODE_UNKNOWN_FLAG     = '501';//:<prefix> <rpl code> <curr nick> :Unknown MODE flag
    public const USERS_DONT_MATCH           = '502';//:<prefix> <rpl code> <curr nick> :Cant change mode for other users

    public const WHO_IS_SECURE = '671';

    public function __construct(string $prefix, int $code, array $args = [], string $comment = null)
    {
        parent::__construct($code, $args, $comment, $prefix);
    }

    protected function resolveComment(): ?string
    {
        switch ($this->getCode()) {
            case self::NO_SUCH_NICK:
                return 'No such nick/channel';
            case self::NO_SUCH_SERVER:
                return 'No such server';
            case self::NO_SUCH_CHANNEL:
                return 'No such channel';
            case self::CANNOT_SEND_TO_CHANNEL:
                return 'Cannot send to channel';
            case self::TOO_MANY_CHANNELS:
                return 'You have joined too many channels';
            case self::WAS_NO_SUCH_NICK:
                return 'There was no such nickname';
            case self::TOO_MANY_TARGETS:
                return 'Duplicate recipients. No message delivered';
            case self::NO_ORIGIN:
                return 'No origin specified';
            case self::NO_RECIPIENT:
                return 'No recipient given (<command>)';
            case self::NO_TEXT_TO_SEND:
                return 'No text to send';
            case self::NO_TOP_LEVEL:
                return 'No toplevel domain specified';
            case self::WILDCARD_TOP_LEVEL:
                return 'Wildcard in toplevel domain';
            case self::UNKNOWN_COMMAND:
                return 'Unknown command';
            case self::NO_MOTD:
                return 'MOTD File is missing';
            case self::NO_ADMIN_INFO:
                return 'No administrative info available';
            case self::FILE_ERROR:
                return 'File error doing <file op> on <file>';
            case self::NO_NICKNAME_GIVEN:
                return 'No nickname given';
            case self::ERRONEOUS_NICKNAME:
                return 'Erroneous nickname';
            case self::NICKNAME_IN_USE:
                return 'Nickname is already in use';
            case self::NICKNAME_COLLISION:
                return 'Nickname collision KILL';
            case self::USER_NOT_IN_CHANNEL:
                return 'They aren’t on that channel';
            case self::NOT_ON_CHANNEL:
                return 'You’re not on that channel';
            case self::USER_ON_CHANNEL:
                return 'is already on channel';
            case self::NO_LOGIN:
                return 'User not logged in';
            case self::SUMMON_DISABLED:
                return 'SUMMON has been disabled';
            case self::USERS_DISABLED:
                return 'USERS has been disabled';
            case self::NOT_REGISTERED:
                return 'You have not registered';
            case self::NEED_MORE_PARAMS:
                return 'Not enough parameters';
            case self::ALREADY_REGISTERED:
                return 'You may not re-register';
            case self::NO_PERM_FOR_HOST:
                return 'Your host isn’t among the privileged';
            case self::PASSWORD_MISMATCH:
                return 'Password incorrect';
            case self::YOU_ARE_BANNED_CREEP:
                return 'You are banned from this server';
            case self::KEY_SET:
                return 'Channel key already set';
            case self::CHANNEL_IS_FULL:
                return 'Cannot join channel (+l)';
            case self::UNKNOWN_MODE:
                return 'is unknown mode char to me';
            case self::INVITE_ONLY_CHANNEL:
                return 'Cannot join channel (+i)';
            case self::BANNED_FROM_CHANNEL:
                return 'Cannot join channel (+b)';
            case self::BAD_CHANNEL_KEY:
                return 'Cannot join channel (+k)';
            case self::NO_PRIVILEGES:
                return 'Permission Denied - You’re not an IRC operator';
            case self::OPERATOR_PRIVILEGES_NEEDED:
                return 'You’re not channel operator';
            case self::CANNOT_KILL_SERVER:
                return 'You cant kill a server!';
            case self::NO_OPERATOR_HOST:
                return 'No O-lines for your host';
            case self::USER_MODE_UNKNOWN_FLAG:
                return 'Unknown MODE flag';
            case self::USERS_DONT_MATCH:
                return 'Cant change mode for other users';
        }
        return null;
    }
}
