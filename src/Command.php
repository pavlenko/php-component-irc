<?php

namespace PE\Component\IRC;

//TODO move here RPL_* and ERR_* codes
class Command
{
    //TODO add here formats
    public const CMD_ADMIN       = 'ADMIN';
    public const CMD_AWAY        = 'AWAY';
    public const CMD_CONNECT     = 'CONNECT';
    public const CMD_ERROR       = 'ERROR';
    public const CMD_INFO        = 'INFO';
    public const CMD_INVITE      = 'INVITE';
    public const CMD_IS_ON       = 'ISON';
    public const CMD_JOIN        = 'JOIN';
    public const CMD_KICK        = 'KICK';
    public const CMD_KILL        = 'KILL';
    public const CMD_LINKS       = 'LINKS';
    public const CMD_LIST        = 'LIST';
    public const CMD_MODE        = 'MODE';
    public const CMD_MOTD        = 'MOTD';//MODT [<target>]
    public const CMD_LIST_USERS  = 'LUSERS';//LUSERS [<mask>[<target>]]
    public const CMD_NAMES       = 'NAMES';
    public const CMD_NICK        = 'NICK';
    public const CMD_NOTICE      = 'NOTICE';
    public const CMD_OPERATOR    = 'OPER';
    public const CMD_PART        = 'PART';
    public const CMD_PASSWORD    = 'PASS';
    public const CMD_PING        = 'PING';
    public const CMD_PONG        = 'PONG';
    public const CMD_PRIVATE_MSG = 'PRIVMSG';
    public const CMD_QUIT        = 'QUIT';
    public const CMD_REHASH      = 'REHASH';
    public const CMD_RESTART     = 'RESTART';
    public const CMD_SERVER      = 'SERVER';
    public const CMD_SERVER_QUIT = 'SQUIT';
    public const CMD_STATS       = 'STATS';
    public const CMD_SUMMON      = 'SUMMON';
    public const CMD_TIME        = 'TIME';
    public const CMD_TOPIC       = 'TOPIC';
    public const CMD_TRACE       = 'TRACE';
    public const CMD_USER_HOST   = 'USERHOST';
    public const CMD_USER        = 'USER';
    public const CMD_USERS       = 'USERS';
    public const CMD_VERSION     = 'VERSION';
    public const CMD_WALLOPS     = 'WALLOPS';
    public const CMD_WHOIS       = 'WHOIS';
    public const CMD_WHO         = 'WHO';
    public const CMD_WHO_WAS     = 'WHOWAS';

    public const ERR_NO_SUCH_NICK               = 401;//<rpl code> <nickname> :No such nick/channel
    public const ERR_NO_SUCH_SERVER             = 402;//<rpl code> <server name> :No such server
    public const ERR_NO_SUCH_CHANNEL            = 403;//<rpl code> <channel name> :No such channel
    public const ERR_CANNOT_SEND_TO_CHANNEL     = 404;//<rpl code> <channel name> :Cannot send to channel
    public const ERR_TOO_MANY_CHANNELS          = 405;//<rpl code> <channel name> :You have joined too many channels
    public const ERR_WAS_NO_SUCH_NICK           = 406;//<rpl code> <nickname> :There was no such nickname
    public const ERR_TOO_MANY_TARGETS           = 407;//<rpl code> <target> :Duplicate recipients. No message delivered
    public const ERR_NO_ORIGIN                  = 409;//<rpl code> :No origin specified
    public const ERR_NO_RECIPIENT               = 411;//<rpl code> :No recipient given (<command>)
    public const ERR_NO_TEXT_TO_SEND            = 412;//<rpl code> :No text to send
    public const ERR_NO_TOP_LEVEL               = 413;//<rpl code> <mask> :No toplevel domain specified
    public const ERR_WILDCARD_TOP_LEVEL         = 414;//<rpl code> <mask> :Wildcard in toplevel domain
    public const ERR_UNKNOWN_COMMAND            = 421;//<rpl code> <command> :Unknown command
    public const ERR_NO_MOTD                    = 422;//<rpl code> :MOTD File is missing
    public const ERR_NO_ADMIN_INFO              = 423;//<rpl code> <server> :No administrative info available
    public const ERR_FILE_ERROR                 = 424;//<rpl code> :File error doing <file op> on <file>
    public const ERR_NO_NICKNAME_GIVEN          = 431;//<rpl code> :No nickname given
    public const ERR_ERRONEOUS_NICKNAME         = 432;//<rpl code> <nick> :Erroneous nickname
    public const ERR_NICKNAME_IN_USE            = 433;//<rpl code> <nick> :Nickname is already in use
    public const ERR_NICKNAME_COLLISION         = 436;//<rpl code> <nick> :Nickname collision KILL
    public const ERR_USER_NOT_IN_CHANNEL        = 441;//<rpl code> <nick> <channel> :They aren’t on that channel
    public const ERR_NOT_ON_CHANNEL             = 442;//<rpl code> <channel> :You’re not on that channel
    public const ERR_USER_ON_CHANNEL            = 443;//<rpl code> <user> <channel> :is already on channel
    public const ERR_NO_LOGIN                   = 444;//<rpl code> <user> :User not logged in
    public const ERR_SUMMON_DISABLED            = 445;//<rpl code> :SUMMON has been disabled
    public const ERR_USERS_DISABLED             = 446;//<rpl code> :USERS has been disabled
    public const ERR_NOT_REGISTERED             = 451;//<rpl code> :You have not registered
    public const ERR_NEED_MORE_PARAMS           = 461;//<rpl code> <command> :Not enough parameters
    public const ERR_ALREADY_REGISTERED         = 462;//<rpl code> :You may not re-register
    public const ERR_NO_PERM_FOR_HOST           = 463;//<rpl code> :Your host isn’t among the privileged
    public const ERR_PASSWORD_MISMATCH          = 464;//<rpl code> :Password incorrect
    public const ERR_YOU_ARE_BANNED_CREEP       = 465;//<rpl code> :You are banned from this server
    public const ERR_KEY_SET                    = 467;//<rpl code> <channel> :Channel key already set
    public const ERR_CHANNEL_IS_FULL            = 471;//<rpl code> <channel> :Cannot join channel (+l)
    public const ERR_UNKNOWN_MODE               = 472;//<rpl code> <char> :is unknown mode char to me
    public const ERR_INVITE_ONLY_CHANNEL        = 473;//<rpl code> <channel> :Cannot join channel (+i)
    public const ERR_BANNED_FROM_CHANNEL        = 474;//<rpl code> <channel> :Cannot join channel (+b)
    public const ERR_BAD_CHANNEL_KEY            = 475;//<rpl code> <channel> :Cannot join channel (+k)
    public const ERR_NO_PRIVILEGES              = 481;//<rpl code> :Permission Denied - You’re not an IRC operator
    public const ERR_OPERATOR_PRIVILEGES_NEEDED = 482;//<rpl code> <channel> :You’re not channel operator
    public const ERR_CANNOT_KILL_SERVER         = 483;//<rpl code> :You cant kill a server!
    public const ERR_NO_OPERATOR_HOST           = 491;//<rpl code> :No O-lines for your host
    public const ERR_U_MODE_UNKNOWN_FLAG        = 501;//<rpl code> :Unknown MODE flag
    public const ERR_USERS_DONT_MATCH           = 502;//<rpl code> :Cant change mode for other users

    public const RPL_WELCOME          = 001;//<rpl code> <nick> :Welcome to the Internet Relay Network <nick>!<user>@<host>
    public const RPL_YOUR_HOST        = 002;//<rpl code> :Your host is <servername>, running version <ver>
    public const RPL_CREATED          = 003;//<rpl code> :This server was created <date>
    public const RPL_MY_INFO          = 004;//<rpl code> :<servername> <version> <available user modes> <available channel modes>
    public const RPL_BOUNCE           = 005;//<rpl code> :Try server <server name>, port <port number>
    public const RPL_NONE             = 300;//Dummy reply number. Not used.
    public const RPL_USER_HOST        = 302;//<rpl code> :[<reply>{<space><reply>}] <reply> ::= <nick>[’*’] ’=’ <’+’|’-’><hostname>
    public const RPL_IS_ON            = 303;//<rpl code> :[<nick>{<space><nick>}]
    public const RPL_AWAY             = 301;//<rpl code> <nick> :<away message>
    public const RPL_UN_AWAY          = 305;//<rpl code> :You are no longer marked as being away
    public const RPL_NOW_AWAY         = 306;//<rpl code> :You have been marked as being away
    public const RPL_WHO_IS_USER      = 311;//<rpl code> <nick> <user> <host> * :<real name>
    public const RPL_WHO_IS_SERVER    = 312;//<rpl code> <nick> <server> :<server info>
    public const RPL_WHO_IS_OPERATOR  = 313;//<rpl code> <nick> :is an IRC operator
    public const RPL_WHO_IS_IDLE      = 317;//<rpl code> <nick> <integer> :seconds idle
    public const RPL_END_OF_WHOIS     = 318;//<rpl code> <nick> :End of /WHOIS list
    public const RPL_WHO_IS_CHANNELS  = 319;//<rpl code> <nick> :{[@|+]<channel><space>}
    public const RPL_WHO_WAS_USER     = 314;//<rpl code> <nick> <user> <host> * :<real name>
    public const RPL_END_OF_WHO_WAS   = 369;//<rpl code> <nick> :End of /WHOWAS
    public const RPL_LIST_START       = 321;//<rpl code> <channel> :<users Name>
    public const RPL_LIST             = 322;//<rpl code> <channel> <# visible> :<topic>
    public const RPL_LIST_END         = 323;//<rpl code> :End of /LIST
    public const RPL_CHANNEL_MODE_IS  = 324;//<rpl code> <channel> <mode> <mode params>
    public const RPL_NO_TOPIC         = 331;//<rpl code> <channel> :No topic is set
    public const RPL_TOPIC            = 332;//<rpl code> <channel> :<topic>
    public const RPL_INVITING         = 341;//<rpl code> <channel> <nick>
    public const RPL_SUMMONING        = 342;//<rpl code> <user> :Summoning user to IRC
    public const RPL_VERSION          = 351;//<rpl code> <version>.<debug level> <server> :<comments>
    public const RPL_WHO_REPLY        = 352;//<rpl code> <channel> <user> <host> <server> <nick> <H|G>[*][@|+] :<hop count> <real name>
    public const RPL_END_OF_WHO       = 315;//<rpl code> <name> :End of /WHO
    public const RPL_NAMES_REPLY      = 353;//<rpl code> <channel> :[[@|+]<nick> [[@|+]<nick> [...]]]
    public const RPL_END_OF_NAMES     = 366;//<rpl code> <channel> :End of /NAMES
    public const RPL_LINKS            = 364;//<rpl code> <mask> <server> :<hop count> <server info>
    public const RPL_END_OF_LINKS     = 365;//<rpl code> <mask> :End of /LINKS
    public const RPL_BAN_LIST         = 367;//<rpl code> <channel> <ban id>
    public const RPL_END_OF_BAN_LIST  = 368;//<rpl code> <channel> :End of channel ban list
    public const RPL_INFO             = 371;//<rpl code> :<string>
    public const RPL_END_OF_INFO      = 374;//<rpl code> :End of /INFO
    public const RPL_MOTD_START       = 375;//<rpl code> :- <server> Message of the day -
    public const RPL_MOTD             = 372;//<rpl code> :- <text>
    public const RPL_END_OF_MOTD      = 376;//<rpl code> :End of /MOTD
    public const RPL_YOU_ARE_OPERATOR = 381;//<rpl code> :You are now an IRC operator
    public const RPL_REHASHING        = 382;//<rpl code> <config file> :Rehashing
    public const RPL_TIME             = 391;//<rpl code> <server> :<string showing server’s local time>
    public const RPL_USERS_START      = 392;//<rpl code> :UserID Terminal Host
    public const RPL_USERS            = 393;//<rpl code> :%-8s %-9s %-8s
    public const RPL_END_OF_USERS     = 394;//<rpl code> :End of users
    public const RPL_NO_USERS         = 395;//<rpl code> :Nobody logged in
    public const RPL_TRACE_LINK       = 200;//<rpl code> Link <version & debug level> <destination> <next server>
    public const RPL_TRACE_CONNECTING = 201;//<rpl code> Try. <class> <server>
    public const RPL_TRACE_HANDSHAKE  = 202;//<rpl code> H.S. <class> <server>
    public const RPL_TRACE_UNKNOWN    = 203;//<rpl code> ???? <class> [<client IP address in dot form>]
    public const RPL_TRACE_OPERATOR   = 204;//<rpl code> Oper <class> <nick>
    public const RPL_TRACE_USER       = 205;//<rpl code> User <class> <nick>
    public const RPL_TRACE_SERVER     = 206;//<rpl code> Serv <class> <int>S <int>C <server> <nick!user|*!*>@<host|server>
    public const RPL_TRACE_NEW_TYPE   = 208;//<rpl code> <new type> 0 <client name>
    public const RPL_TRACE_LOG        = 261;//<rpl code> File <logfile> <debug level>
    public const RPL_STATS_LINK_INFO  = 211;//<rpl code> <link name> <send q> <sent messages> <sent bytes> <received messages> <received bytes> <time open>
    public const RPL_STATS_COMMANDS   = 212;//<rpl code> <command> <count>
    public const RPL_STATS_C_LINE     = 213;//<rpl code> C <host> * <name> <port> <class>
    public const RPL_STATS_N_LINE     = 214;//<rpl code> N <host> * <name> <port> <class>
    public const RPL_STATS_I_LINE     = 215;//<rpl code> I <host> * <host> <port> <class>
    public const RPL_STATS_K_LINE     = 216;//<rpl code> K <host> * <username> <port> <class>
    public const RPL_STATS_Y_LINE     = 218;//<rpl code> Y <class> <ping frequency> <connect frequency> <max send q>
    public const RPL_END_OF_STATS     = 219;//<rpl code> <stats letter> :End of /STATS report
    public const RPL_STATS_L_LINE     = 241;//<rpl code> L <host mask> * <servername> <max depth>
    public const RPL_STATS_UPTIME     = 242;//<rpl code> :Server Up %d days %d:%02d:%02d
    public const RPL_STATS_O_LINE     = 243;//<rpl code> O <host mask> * <name>
    public const RPL_STATS_H_LINE     = 244;//<rpl code> H <host mask> * <servername>
    public const RPL_USER_MODE_IS     = 221;//<rpl code> <user mode string>
    public const RPL_L_USER_CLIENT    = 251;//<rpl code> :There are <integer> users and <integer> invisible on <integer> servers
    public const RPL_L_USER_OPERATORS = 252;//<rpl code> <integer> :operator(s) online
    public const RPL_L_USER_UNKNOWN   = 253;//<rpl code> <integer> :unknown connection(s)
    public const RPL_L_USER_CHANNELS  = 254;//<rpl code> <integer> :channels formed
    public const RPL_L_USER_ME        = 255;//<rpl code> :I have <integer> clients and <integer> servers
    public const RPL_ADMIN_ME         = 256;//<rpl code> <server> :Administrative info
    public const RPL_ADMIN_LOC1       = 257;//<rpl code> :<admin info>
    public const RPL_ADMIN_LOC2       = 258;//<rpl code> :<admin info>
    public const RPL_ADMIN_EMAIL      = 259;//<rpl code> :<admin info>

    private string  $code;
    private array   $args;
    private ?string $comment;
    private ?string $prefix;

    public function __construct(string $code, array $args = [], ?string $comment = null, ?string $prefix = null)
    {
        $this->code    = is_numeric($code) ? sprintf('%03d', $code) : $code;
        $this->args    = $args;
        $this->comment = $comment ?: $this->resolveComment($code);
        $this->prefix  = $prefix;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function getArg(int $index)
    {
        return $this->args[$index] ?? null;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function __toString()
    {
        $parts = [];
        if ($this->prefix) {
            $parts[] = ':' . $this->prefix;
        }
        array_push($parts, $this->code, ...$this->args);
        if ($this->comment) {
            $parts[] = ':' . $this->comment;
        }
        return implode(' ', $parts);
    }

    private function resolveComment(string $name): ?string
    {
        switch ($name) {
            case self::RPL_UN_AWAY:
                return 'You are no longer marked as being away';
            case self::RPL_NOW_AWAY:
                return 'You have been marked as being away';
            case self::RPL_WHO_IS_OPERATOR:
                return 'is an IRC operator';
            case self::RPL_WHO_IS_IDLE:
                return 'seconds idle';
            case self::RPL_END_OF_WHOIS:
                return 'End of /WHOIS list';
            case self::RPL_END_OF_WHO_WAS:
                return 'End of /WHOWAS';
            case self::RPL_LIST_END:
                return 'End of /LIST';
            case self::RPL_NO_TOPIC:
                return 'No topic is set';
            case self::RPL_SUMMONING:
                return 'Summoning user to IRC';
            case self::RPL_END_OF_WHO:
                return 'End of /WHO';
            case self::RPL_END_OF_NAMES:
                return 'End of /NAMES';
            case self::RPL_END_OF_LINKS:
                return 'End of /LINKS';
            case self::RPL_END_OF_BAN_LIST:
                return 'End of channel ban list';//TODO which command reply
            case self::RPL_END_OF_INFO:
                return 'End of /INFO';
            case self::RPL_END_OF_MOTD:
                return 'End of /MOTD command';
            case self::RPL_YOU_ARE_OPERATOR:
                return 'You are now an IRC operator';
            case self::RPL_REHASHING:
                return 'Rehashing';
            case self::RPL_USERS_START:
                return 'UserID Terminal Host';
            case self::RPL_END_OF_USERS:
                return 'End of /USERS';
            case self::RPL_NO_USERS:
                return 'Nobody logged in';
            case self::RPL_L_USER_OPERATORS:
                return 'operator(s) online';
            case self::RPL_L_USER_UNKNOWN:
                return 'unknown connection(s)';
            case self::RPL_L_USER_CHANNELS:
                return 'channels formed';
            case self::RPL_ADMIN_ME:
                return 'Administrative info';

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