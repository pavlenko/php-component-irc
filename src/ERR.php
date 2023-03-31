<?php

namespace PE\Component\IRC;

final class ERR extends MSG
{
    public const ERR_NO_SUCH_NICK               = 401;//:<prefix> <rpl code> <curr nick> <nickname> :No such nick/channel
    public const ERR_NO_SUCH_SERVER             = 402;//:<prefix> <rpl code> <curr nick> <server name> :No such server
    public const ERR_NO_SUCH_CHANNEL            = 403;//:<prefix> <rpl code> <curr nick> <channel name> :No such channel
    public const ERR_CANNOT_SEND_TO_CHANNEL     = 404;//:<prefix> <rpl code> <curr nick> <channel name> :Cannot send to channel
    public const ERR_TOO_MANY_CHANNELS          = 405;//:<prefix> <rpl code> <curr nick> <channel name> :You have joined too many channels
    public const ERR_WAS_NO_SUCH_NICK           = 406;//:<prefix> <rpl code> <curr nick> <nickname> :There was no such nickname
    public const ERR_TOO_MANY_TARGETS           = 407;//:<prefix> <rpl code> <curr nick> <target> :Duplicate recipients. No message delivered
    public const ERR_NO_ORIGIN                  = 409;//:<prefix> <rpl code> <curr nick> :No origin specified
    public const ERR_NO_RECIPIENT               = 411;//:<prefix> <rpl code> <curr nick> :No recipient given (<command>)
    public const ERR_NO_TEXT_TO_SEND            = 412;//:<prefix> <rpl code> <curr nick> :No text to send
    public const ERR_NO_TOP_LEVEL               = 413;//:<prefix> <rpl code> <curr nick> <mask> :No toplevel domain specified
    public const ERR_WILDCARD_TOP_LEVEL         = 414;//:<prefix> <rpl code> <curr nick> <mask> :Wildcard in toplevel domain
    public const ERR_UNKNOWN_COMMAND            = 421;//:<prefix> <rpl code> <curr nick> <command> :Unknown command
    public const ERR_NO_MOTD                    = 422;//:<prefix> <rpl code> <curr nick> :MOTD File is missing
    public const ERR_NO_ADMIN_INFO              = 423;//:<prefix> <rpl code> <curr nick> <server> :No administrative info available
    public const ERR_FILE_ERROR                 = 424;//:<prefix> <rpl code> <curr nick> :File error doing <file op> on <file>
    public const ERR_NO_NICKNAME_GIVEN          = 431;//:<prefix> <rpl code> <curr nick> :No nickname given
    public const ERR_ERRONEOUS_NICKNAME         = 432;//:<prefix> <rpl code> <curr nick> <nick> :Erroneous nickname
    public const ERR_NICKNAME_IN_USE            = 433;//:<prefix> <rpl code> <curr nick> <nick> :Nickname is already in use
    public const ERR_NICKNAME_COLLISION         = 436;//:<prefix> <rpl code> <curr nick> <nick> :Nickname collision KILL
    public const ERR_USER_NOT_IN_CHANNEL        = 441;//:<prefix> <rpl code> <curr nick> <nick> <channel> :They aren’t on that channel
    public const ERR_NOT_ON_CHANNEL             = 442;//:<prefix> <rpl code> <curr nick> <channel> :You’re not on that channel
    public const ERR_USER_ON_CHANNEL            = 443;//:<prefix> <rpl code> <curr nick> <user> <channel> :is already on channel
    public const ERR_NO_LOGIN                   = 444;//:<prefix> <rpl code> <curr nick> <user> :User not logged in
    public const ERR_SUMMON_DISABLED            = 445;//:<prefix> <rpl code> <curr nick> :SUMMON has been disabled
    public const ERR_USERS_DISABLED             = 446;//:<prefix> <rpl code> <curr nick> :USERS has been disabled
    public const ERR_NOT_REGISTERED             = 451;//:<prefix> <rpl code> <curr nick> :You have not registered
    public const ERR_NEED_MORE_PARAMS           = 461;//:<prefix> <rpl code> <curr nick> <command> :Not enough parameters
    public const ERR_ALREADY_REGISTERED         = 462;//:<prefix> <rpl code> <curr nick> :You may not re-register
    public const ERR_NO_PERM_FOR_HOST           = 463;//:<prefix> <rpl code> <curr nick> :Your host isn’t among the privileged
    public const ERR_PASSWORD_MISMATCH          = 464;//:<prefix> <rpl code> <curr nick> :Password incorrect
    public const ERR_YOU_ARE_BANNED_CREEP       = 465;//:<prefix> <rpl code> <curr nick> :You are banned from this server
    public const ERR_KEY_SET                    = 467;//:<prefix> <rpl code> <curr nick> <channel> :Channel key already set
    public const ERR_CHANNEL_IS_FULL            = 471;//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+l)
    public const ERR_UNKNOWN_MODE               = 472;//:<prefix> <rpl code> <curr nick> <char> :is unknown mode char to me
    public const ERR_INVITE_ONLY_CHANNEL        = 473;//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+i)
    public const ERR_BANNED_FROM_CHANNEL        = 474;//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+b)
    public const ERR_BAD_CHANNEL_KEY            = 475;//:<prefix> <rpl code> <curr nick> <channel> :Cannot join channel (+k)
    public const ERR_NO_PRIVILEGES              = 481;//:<prefix> <rpl code> <curr nick> :Permission Denied - You’re not an IRC operator
    public const ERR_OPERATOR_PRIVILEGES_NEEDED = 482;//:<prefix> <rpl code> <curr nick> <channel> :You’re not channel operator
    public const ERR_CANNOT_KILL_SERVER         = 483;//:<prefix> <rpl code> <curr nick> :You cant kill a server!
    public const ERR_NO_OPERATOR_HOST           = 491;//:<prefix> <rpl code> <curr nick> :No O-lines for your host
    public const ERR_U_MODE_UNKNOWN_FLAG        = 501;//:<prefix> <rpl code> <curr nick> :Unknown MODE flag
    public const ERR_USERS_DONT_MATCH           = 502;//:<prefix> <rpl code> <curr nick> :Cant change mode for other users

    public function __construct(string $prefix, int $code, array $args = [], string $comment = null)
    {
        parent::__construct($code, $args, $comment, $prefix);
    }

    protected function resolveComment(): ?string
    {
        switch ($this->getCode()) {
            case self::ERR_NO_SUCH_NICK:
                return 'No such nick/channel';
            case self::ERR_NO_SUCH_SERVER:
                return 'No such server';
            case self::ERR_NO_SUCH_CHANNEL:
                return 'No such channel';
            case self::ERR_CANNOT_SEND_TO_CHANNEL:
                return 'Cannot send to channel';
            case self::ERR_TOO_MANY_CHANNELS:
                return 'You have joined too many channels';
            case self::ERR_WAS_NO_SUCH_NICK:
                return 'There was no such nickname';
            case self::ERR_TOO_MANY_TARGETS:
                return 'Duplicate recipients. No message delivered';
            case self::ERR_NO_ORIGIN:
                return 'No origin specified';
            case self::ERR_NO_RECIPIENT:
                return 'No recipient given (<command>)';
            case self::ERR_NO_TEXT_TO_SEND:
                return 'No text to send';
            case self::ERR_NO_TOP_LEVEL:
                return 'No toplevel domain specified';
            case self::ERR_WILDCARD_TOP_LEVEL:
                return 'Wildcard in toplevel domain';
            case self::ERR_UNKNOWN_COMMAND:
                return 'Unknown command';
            case self::ERR_NO_MOTD:
                return 'MOTD File is missing';
            case self::ERR_NO_ADMIN_INFO:
                return 'No administrative info available';
            case self::ERR_FILE_ERROR:
                return 'File error doing <file op> on <file>';
            case self::ERR_NO_NICKNAME_GIVEN:
                return 'No nickname given';
            case self::ERR_ERRONEOUS_NICKNAME:
                return 'Erroneous nickname';
            case self::ERR_NICKNAME_IN_USE:
                return 'Nickname is already in use';
            case self::ERR_NICKNAME_COLLISION:
                return 'Nickname collision KILL';
            case self::ERR_USER_NOT_IN_CHANNEL:
                return 'They aren’t on that channel';
            case self::ERR_NOT_ON_CHANNEL:
                return 'You’re not on that channel';
            case self::ERR_USER_ON_CHANNEL:
                return 'is already on channel';
            case self::ERR_NO_LOGIN:
                return 'User not logged in';
            case self::ERR_SUMMON_DISABLED:
                return 'SUMMON has been disabled';
            case self::ERR_USERS_DISABLED:
                return 'USERS has been disabled';
            case self::ERR_NOT_REGISTERED:
                return 'You have not registered';
            case self::ERR_NEED_MORE_PARAMS:
                return 'Not enough parameters';
            case self::ERR_ALREADY_REGISTERED:
                return 'You may not re-register';
            case self::ERR_NO_PERM_FOR_HOST:
                return 'Your host isn’t among the privileged';
            case self::ERR_PASSWORD_MISMATCH:
                return 'Password incorrect';
            case self::ERR_YOU_ARE_BANNED_CREEP:
                return 'You are banned from this server';
            case self::ERR_KEY_SET:
                return 'Channel key already set';
            case self::ERR_CHANNEL_IS_FULL:
                return 'Cannot join channel (+l)';
            case self::ERR_UNKNOWN_MODE:
                return 'is unknown mode char to me';
            case self::ERR_INVITE_ONLY_CHANNEL:
                return 'Cannot join channel (+i)';
            case self::ERR_BANNED_FROM_CHANNEL:
                return 'Cannot join channel (+b)';
            case self::ERR_BAD_CHANNEL_KEY:
                return 'Cannot join channel (+k)';
            case self::ERR_NO_PRIVILEGES:
                return 'Permission Denied - You’re not an IRC operator';
            case self::ERR_OPERATOR_PRIVILEGES_NEEDED:
                return 'You’re not channel operator';
            case self::ERR_CANNOT_KILL_SERVER:
                return 'You cant kill a server!';
            case self::ERR_NO_OPERATOR_HOST:
                return 'No O-lines for your host';
            case self::ERR_U_MODE_UNKNOWN_FLAG:
                return 'Unknown MODE flag';
            case self::ERR_USERS_DONT_MATCH:
                return 'Cant change mode for other users';
        }
        return null;
    }
}
