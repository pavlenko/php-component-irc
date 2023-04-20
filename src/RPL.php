<?php

namespace PE\Component\IRC;

final class RPL extends MSG
{
    public const WELCOME          = 001;//:<prefix> <rpl code> <nick> :Welcome to the Internet Relay Network <nick>!<user>@<host>
    public const YOUR_HOST        = 002;//:<prefix> <rpl code> <nick> :Your host is <servername>, running version <ver>
    public const CREATED          = 003;//:<prefix> <rpl code> <nick> :This server was created <date>
    public const MY_INFO          = 004;//:<prefix> <rpl code> <servername> <version> <available user modes> <available channel modes> [<channel modes that take a parameter>]
    public const BOUNCE           = 005;//:<prefix> <rpl code> :Try server <server name>, port <port number>
    public const TRACE_LINK       = 200;//:<prefix> <rpl code> Link <version & debug level> <destination> <next server>
    public const TRACE_CONNECTING = 201;//:<prefix> <rpl code> Try. <class> <server>
    public const TRACE_HANDSHAKE  = 202;//:<prefix> <rpl code> H.S. <class> <server>
    public const TRACE_UNKNOWN    = 203;//:<prefix> <rpl code> ???? <class> [<client IP address in dot form>]
    public const TRACE_OPERATOR   = 204;//:<prefix> <rpl code> Oper <class> <nick>
    public const TRACE_USER       = 205;//:<prefix> <rpl code> User <class> <nick>
    public const TRACE_SERVER     = 206;//:<prefix> <rpl code> Serv <class> <int>S <int>C <server> <nick!user|*!*>@<host|server>
    public const TRACE_NEW_TYPE   = 208;//:<prefix> <rpl code> <new type> 0 <client name>
    public const TRACE_LOG        = 261;//:<prefix> <rpl code> File <logfile> <debug level>
    public const STATS_LINK_INFO  = 211;//:<prefix> <rpl code> <link name> <send q> <sent messages> <sent bytes> <received messages> <received bytes> <time open>
    public const STATS_COMMANDS   = 212;//:<prefix> <rpl code> <command> <count>
    public const STATS_C_LINE     = 213;//:<prefix> <rpl code> C <host> * <name> <port> <class>
    public const STATS_N_LINE     = 214;//:<prefix> <rpl code> N <host> * <name> <port> <class>
    public const STATS_I_LINE     = 215;//:<prefix> <rpl code> I <host> * <host> <port> <class>
    public const STATS_K_LINE     = 216;//:<prefix> <rpl code> K <host> * <username> <port> <class>
    public const STATS_Y_LINE     = 218;//:<prefix> <rpl code> Y <class> <ping frequency> <connect frequency> <max send q>
    public const END_OF_STATS     = 219;//:<prefix> <rpl code> <stats letter> :End of /STATS report
    public const USER_MODE_IS     = 221;//:<prefix> <rpl code> <user mode string>
    public const STATS_L_LINE     = 241;//:<prefix> <rpl code> L <host mask> * <servername> <max depth>
    public const STATS_UPTIME     = 242;//:<prefix> <rpl code> :Server Up %d days %d:%02d:%02d
    public const STATS_O_LINE     = 243;//:<prefix> <rpl code> O <host mask> * <name>
    public const STATS_H_LINE     = 244;//:<prefix> <rpl code> H <host mask> * <servername>
    public const STATS_S_LINE     = 245;//:<prefix> <rpl code>//TODO
    //TODO are below valid reply name
    public const STATS_D_LINE     = 250;//:<prefix> <rpl code> :Highest connection count: N (N clients) (N connections received)
    public const L_USER_CLIENT    = 251;//:<prefix> <rpl code> :There are <integer> users and <integer> invisible on <integer> servers
    public const L_USER_OPERATORS = 252;//:<prefix> <rpl code> <integer> :operator(s) online
    public const L_USER_UNKNOWN   = 253;//:<prefix> <rpl code> <integer> :unknown connection(s)
    public const L_USER_CHANNELS  = 254;//:<prefix> <rpl code> <integer> :channels formed
    public const L_USER_ME        = 255;//:<prefix> <rpl code> :I have <integer> clients and <integer> servers
    public const ADMIN_ME         = 256;//:<prefix> <rpl code> <server> :Administrative info
    public const ADMIN_LOC1       = 257;//:<prefix> <rpl code> :<admin info>
    public const ADMIN_LOC2       = 258;//:<prefix> <rpl code> :<admin info>
    public const ADMIN_EMAIL      = 259;//:<prefix> <rpl code> :<admin info>
    public const NONE             = 300;//Dummy reply number. Not used.
    public const USER_HOST        = 302;//:<prefix> <rpl code> :[<reply>{<space><reply>}] <reply> ::= <nick>[*]=<+|-><nick><hostname>
    public const IS_ON            = 303;//:<prefix> <rpl code> :[<nick>{<space><nick>}]
    public const AWAY             = 301;//:<prefix> <rpl code> <nick> :<away message>
    public const UN_AWAY          = 305;//:<prefix> <rpl code> :You are no longer marked as being away
    public const NOW_AWAY         = 306;//:<prefix> <rpl code> :You have been marked as being away
    public const WHO_IS_USER      = 311;//:<prefix> <rpl code> <nick> <user> <host> * :<real name>
    public const WHO_IS_SERVER    = 312;//:<prefix> <rpl code> <nick> <server> :<server info>
    public const WHO_IS_OPERATOR  = 313;//:<prefix> <rpl code> <nick> :is an IRC operator
    public const WHO_IS_IDLE      = 317;//:<prefix> <rpl code> <nick> <integer> :seconds idle
    public const END_OF_WHO_IS    = 318;//:<prefix> <rpl code> <nick> :End of /WHOIS list
    public const WHO_IS_CHANNELS  = 319;//:<prefix> <rpl code> <nick> :{[@|+]<channel><space>}
    public const WHO_WAS_USER     = 314;//:<prefix> <rpl code> <nick> <user> <host> * :<real name>
    public const END_OF_WHO_WAS   = 369;//:<prefix> <rpl code> <nick> :End of /WHOWAS
    public const LIST_START       = 321;//:<prefix> <rpl code> "Channel" :Users Name
    public const LIST             = 322;//:<prefix> <rpl code> <channel> <# visible> :<topic>
    public const LIST_END         = 323;//:<prefix> <rpl code> :End of /LIST
    public const CHANNEL_MODE_IS  = 324;//:<prefix> <rpl code> <channel> <mode> <mode params>
    public const NO_TOPIC         = 331;//:<prefix> <rpl code> <channel> :No topic is set
    public const TOPIC            = 332;//:<prefix> <rpl code> <channel> :<topic>
    public const INVITING         = 341;//:<prefix> <rpl code> <nick> <channel>
    public const SUMMONING        = 342;//:<prefix> <rpl code> <user> :Summoning user to IRC
    public const VERSION          = 351;//:<prefix> <rpl code> <version>.<debug level> <server> :<comments>
    public const WHO_REPLY        = 352;//:<prefix> <rpl code> <channel> <user> <host> <server> <nick> <H|G>[*][@|+] :<hop count> <real name>
    public const END_OF_WHO       = 315;//:<prefix> <rpl code> <name> :End of /WHO
    public const NAMES_REPLY      = 353;//:<prefix> <rpl code> <channel> :[[@|+]<nick> [[@|+]<nick> [...]]]
    public const END_OF_NAMES     = 366;//:<prefix> <rpl code> <channel> :End of /NAMES
    public const LINKS            = 364;//:<prefix> <rpl code> <mask> <server> :<hop count> <server info>
    public const END_OF_LINKS     = 365;//:<prefix> <rpl code> <mask> :End of /LINKS
    public const BAN_LIST         = 367;//:<prefix> <rpl code> <channel> <ban id>
    public const END_OF_BAN_LIST  = 368;//:<prefix> <rpl code> <channel> :End of channel ban list
    public const INFO             = 371;//:<prefix> <rpl code> :<string>
    public const END_OF_INFO      = 374;//:<prefix> <rpl code> :End of /INFO
    public const MOTD_START       = 375;//:<prefix> <rpl code> <nick> :- <server> Message of the day -
    public const MOTD             = 372;//:<prefix> <rpl code> <nick> :- <text>
    public const END_OF_MOTD      = 376;//:<prefix> <rpl code> <nick> :End of /MOTD
    public const YOU_ARE_OPERATOR = 381;//:<prefix> <rpl code> :You are now an IRC operator
    public const REHASHING        = 382;//:<prefix> <rpl code> <config file> :Rehashing
    public const TIME             = 391;//:<prefix> <rpl code> <server> :<string showing server’s local time>
    public const USERS_START      = 392;//:<prefix> <rpl code> :UserID Terminal Host
    public const USERS            = 393;//:<prefix> <rpl code> :%-8s %-9s %-8s
    public const END_OF_USERS     = 394;//:<prefix> <rpl code> :End of users
    public const NO_USERS         = 395;//:<prefix> <rpl code> :Nobody logged in

    public function __construct(string $prefix, int $code, array $args = [], string $comment = null)
    {
        parent::__construct($code, $args, $comment, $prefix);
    }

    protected function resolveComment(): ?string
    {
        switch ($this->getCode()) {
            case self::WELCOME          :
                return 'Welcome to the Internet Relay Network';
            case self::YOUR_HOST        :
            case self::END_OF_STATS     :
                return 'End of /STATS report';
            case self::L_USER_OPERATORS :
                return 'operator(s) online';
            case self::L_USER_UNKNOWN   :
                return 'unknown connection(s)';
            case self::L_USER_CHANNELS  :
                return 'channels formed';
            case self::ADMIN_ME         :
                return 'Administrative info';
            case self::UN_AWAY          :
                return 'You are no longer marked as being away';
            case self::NOW_AWAY         :
                return 'You have been marked as being away';
            case self::WHO_IS_OPERATOR  :
                return 'is an IRC operator';
            case self::WHO_IS_IDLE      :
                return 'seconds idle';
            case self::END_OF_WHO_IS     :
                return 'End of /WHOIS';
            case self::END_OF_WHO_WAS   :
                return 'End of /WHOWAS';
            case self::LIST_END         :
                return 'End of /LIST';
            case self::NO_TOPIC         :
                return 'No topic is set';
            case self::SUMMONING        :
                return 'Summoning user to IRC';
            case self::END_OF_WHO       :
                return 'End of /WHO';
            case self::END_OF_NAMES     :
                return 'End of /NAMES';
            case self::END_OF_LINKS     :
                return 'End of /LINKS';
            case self::END_OF_BAN_LIST  :
                return 'End of channel ban list';
            case self::END_OF_INFO      :
                return 'End of /INFO';
            case self::END_OF_MOTD      :
                return 'End of /MOTD';
            case self::YOU_ARE_OPERATOR :
                return 'You are now an IRC operator';
            case self::REHASHING        :
                return 'Rehashing';
            case self::USERS_START      :
                return 'UserID Terminal Host';
            case self::END_OF_USERS     :
                return 'End of /USERS';
            case self::NO_USERS:
                return 'Nobody logged in';
        }
        return null;
    }
}
