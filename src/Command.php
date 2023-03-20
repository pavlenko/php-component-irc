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

    public const RPL_WELCOME          = 001;//<rpl code> <nick> :Welcome to the Internet Relay Network <nick>!<user>@<host>
    public const RPL_YOUR_HOST        = 002;//<rpl code> :Your host is <servername>, running version <ver>
    public const RPL_CREATED          = 003;//<rpl code> :This server was created <date>
    public const RPL_MY_INFO          = 004;//<rpl code> <servername> <version> <available user modes> <available channel modes>
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

    private string  $name;
    private array   $args;
    private ?string $comment;
    private ?string $prefix;

    public function __construct(string $name, array $args = [], ?string $comment = null, ?string $prefix = null)
    {
        $this->name    = is_numeric($name) ? sprintf('%03d', $name) : $name;
        $this->args    = $args;
        $this->comment = $comment ?: $this->resolveComment($name);
        $this->prefix  = $prefix;
    }

    public function getName(): string
    {
        return $this->name;
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
        array_push($parts, $this->name, ...$this->args);
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
            case self::RPL_LIST_END:
                return 'End of /LIST';
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
                return 'End of /MOTD';
            case self::RPL_YOU_ARE_OPERATOR:
                return 'You are now an IRC operator';
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
        }
        return null;
    }
}