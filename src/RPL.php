<?php

namespace PE\Component\IRC;

final class RPL extends MSG
{
    public const RPL_WELCOME          = 001;//:<prefix> <rpl code> <nick> :Welcome to the Internet Relay Network <nick>!<user>@<host>
    public const RPL_YOUR_HOST        = 002;//:<prefix> <rpl code> <nick> :Your host is <servername>, running version <ver>
    public const RPL_CREATED          = 003;//:<prefix> <rpl code> <nick> :This server was created <date>
    public const RPL_MY_INFO          = 004;//:<prefix> <rpl code> <servername> <version> <available user modes> <available channel modes> [<channel modes that take a parameter>]
    public const RPL_BOUNCE           = 005;//:<prefix> <rpl code> :Try server <server name>, port <port number>
    public const RPL_NONE             = 300;//Dummy reply number. Not used.
    public const RPL_USER_HOST        = 302;//:<prefix> <rpl code> :[<reply>{<space><reply>}] <reply> ::= <nick>[’*’] ’=’ <’+’|’-’><hostname>
    public const RPL_IS_ON            = 303;//:<prefix> <rpl code> :[<nick>{<space><nick>}]
    public const RPL_AWAY             = 301;//:<prefix> <rpl code> <nick> :<away message>
    public const RPL_UN_AWAY          = 305;//:<prefix> <rpl code> :You are no longer marked as being away
    public const RPL_NOW_AWAY         = 306;//:<prefix> <rpl code> :You have been marked as being away
    public const RPL_WHO_IS_USER      = 311;//:<prefix> <rpl code> <nick> <user> <host> * :<real name>
    public const RPL_WHO_IS_SERVER    = 312;//:<prefix> <rpl code> <nick> <server> :<server info>
    public const RPL_WHO_IS_OPERATOR  = 313;//:<prefix> <rpl code> <nick> :is an IRC operator
    public const RPL_WHO_IS_IDLE      = 317;//:<prefix> <rpl code> <nick> <integer> :seconds idle
    public const RPL_END_OF_WHOIS     = 318;//:<prefix> <rpl code> <nick> :End of /WHOIS list
    public const RPL_WHO_IS_CHANNELS  = 319;//:<prefix> <rpl code> <nick> :{[@|+]<channel><space>}
    public const RPL_WHO_WAS_USER     = 314;//:<prefix> <rpl code> <nick> <user> <host> * :<real name>
    public const RPL_END_OF_WHO_WAS   = 369;//:<prefix> <rpl code> <nick> :End of /WHOWAS
    public const RPL_LIST_START       = 321;//:<prefix> <rpl code> <channel> :<users Name>
    public const RPL_LIST             = 322;//:<prefix> <rpl code> <channel> <# visible> :<topic>
    public const RPL_LIST_END         = 323;//:<prefix> <rpl code> :End of /LIST
    public const RPL_CHANNEL_MODE_IS  = 324;//:<prefix> <rpl code> <channel> <mode> <mode params>
    public const RPL_NO_TOPIC         = 331;//:<prefix> <rpl code> <channel> :No topic is set
    public const RPL_TOPIC            = 332;//:<prefix> <rpl code> <channel> :<topic>
    public const RPL_INVITING         = 341;//:<prefix> <rpl code> <channel> <nick>
    public const RPL_SUMMONING        = 342;//:<prefix> <rpl code> <user> :Summoning user to IRC
    public const RPL_VERSION          = 351;//:<prefix> <rpl code> <version>.<debug level> <server> :<comments>
    public const RPL_WHO_REPLY        = 352;//:<prefix> <rpl code> <channel> <user> <host> <server> <nick> <H|G>[*][@|+] :<hop count> <real name>
    public const RPL_END_OF_WHO       = 315;//:<prefix> <rpl code> <name> :End of /WHO
    public const RPL_NAMES_REPLY      = 353;//:<prefix> <rpl code> <channel> :[[@|+]<nick> [[@|+]<nick> [...]]]
    public const RPL_END_OF_NAMES     = 366;//:<prefix> <rpl code> <channel> :End of /NAMES
    public const RPL_LINKS            = 364;//:<prefix> <rpl code> <mask> <server> :<hop count> <server info>
    public const RPL_END_OF_LINKS     = 365;//:<prefix> <rpl code> <mask> :End of /LINKS
    public const RPL_BAN_LIST         = 367;//:<prefix> <rpl code> <channel> <ban id>
    public const RPL_END_OF_BAN_LIST  = 368;//:<prefix> <rpl code> <channel> :End of channel ban list
    public const RPL_INFO             = 371;//:<prefix> <rpl code> :<string>
    public const RPL_END_OF_INFO      = 374;//:<prefix> <rpl code> :End of /INFO
    public const RPL_MOTD_START       = 375;//:<prefix> <rpl code> <nick> :- <server> Message of the day -
    public const RPL_MOTD             = 372;//:<prefix> <rpl code> <nick> :- <text>
    public const RPL_END_OF_MOTD      = 376;//:<prefix> <rpl code> <nick> :End of /MOTD
    public const RPL_YOU_ARE_OPERATOR = 381;//:<prefix> <rpl code> :You are now an IRC operator
    public const RPL_REHASHING        = 382;//:<prefix> <rpl code> <config file> :Rehashing
    public const RPL_TIME             = 391;//:<prefix> <rpl code> <server> :<string showing server’s local time>
    public const RPL_USERS_START      = 392;//:<prefix> <rpl code> :UserID Terminal Host
    public const RPL_USERS            = 393;//:<prefix> <rpl code> :%-8s %-9s %-8s
    public const RPL_END_OF_USERS     = 394;//:<prefix> <rpl code> :End of users
    public const RPL_NO_USERS         = 395;//:<prefix> <rpl code> :Nobody logged in
    public const RPL_TRACE_LINK       = 200;//:<prefix> <rpl code> Link <version & debug level> <destination> <next server>
    public const RPL_TRACE_CONNECTING = 201;//:<prefix> <rpl code> Try. <class> <server>
    public const RPL_TRACE_HANDSHAKE  = 202;//:<prefix> <rpl code> H.S. <class> <server>
    public const RPL_TRACE_UNKNOWN    = 203;//:<prefix> <rpl code> ???? <class> [<client IP address in dot form>]
    public const RPL_TRACE_OPERATOR   = 204;//:<prefix> <rpl code> Oper <class> <nick>
    public const RPL_TRACE_USER       = 205;//:<prefix> <rpl code> User <class> <nick>
    public const RPL_TRACE_SERVER     = 206;//:<prefix> <rpl code> Serv <class> <int>S <int>C <server> <nick!user|*!*>@<host|server>
    public const RPL_TRACE_NEW_TYPE   = 208;//:<prefix> <rpl code> <new type> 0 <client name>
    public const RPL_TRACE_LOG        = 261;//:<prefix> <rpl code> File <logfile> <debug level>
    public const RPL_STATS_LINK_INFO  = 211;//:<prefix> <rpl code> <link name> <send q> <sent messages> <sent bytes> <received messages> <received bytes> <time open>
    public const RPL_STATS_COMMANDS   = 212;//:<prefix> <rpl code> <command> <count>
    public const RPL_STATS_C_LINE     = 213;//:<prefix> <rpl code> C <host> * <name> <port> <class>
    public const RPL_STATS_N_LINE     = 214;//:<prefix> <rpl code> N <host> * <name> <port> <class>
    public const RPL_STATS_I_LINE     = 215;//:<prefix> <rpl code> I <host> * <host> <port> <class>
    public const RPL_STATS_K_LINE     = 216;//:<prefix> <rpl code> K <host> * <username> <port> <class>
    public const RPL_STATS_Y_LINE     = 218;//:<prefix> <rpl code> Y <class> <ping frequency> <connect frequency> <max send q>
    public const RPL_END_OF_STATS     = 219;//:<prefix> <rpl code> <stats letter> :End of /STATS report
    public const RPL_STATS_L_LINE     = 241;//:<prefix> <rpl code> L <host mask> * <servername> <max depth>
    public const RPL_STATS_UPTIME     = 242;//:<prefix> <rpl code> :Server Up %d days %d:%02d:%02d
    public const RPL_STATS_O_LINE     = 243;//:<prefix> <rpl code> O <host mask> * <name>
    public const RPL_STATS_H_LINE     = 244;//:<prefix> <rpl code> H <host mask> * <servername>
    public const RPL_USER_MODE_IS     = 221;//:<prefix> <rpl code> <user mode string>
    public const RPL_L_USER_CLIENT    = 251;//:<prefix> <rpl code> :There are <integer> users and <integer> invisible on <integer> servers
    public const RPL_L_USER_OPERATORS = 252;//:<prefix> <rpl code> <integer> :operator(s) online
    public const RPL_L_USER_UNKNOWN   = 253;//:<prefix> <rpl code> <integer> :unknown connection(s)
    public const RPL_L_USER_CHANNELS  = 254;//:<prefix> <rpl code> <integer> :channels formed
    public const RPL_L_USER_ME        = 255;//:<prefix> <rpl code> :I have <integer> clients and <integer> servers
    public const RPL_ADMIN_ME         = 256;//:<prefix> <rpl code> <server> :Administrative info
    public const RPL_ADMIN_LOC1       = 257;//:<prefix> <rpl code> :<admin info>
    public const RPL_ADMIN_LOC2       = 258;//:<prefix> <rpl code> :<admin info>
    public const RPL_ADMIN_EMAIL      = 259;//:<prefix> <rpl code> :<admin info>

    public function __construct(string $prefix, int $code, array $args = [], string $comment = null)
    {
        parent::__construct($code, $args, $comment, $prefix);
    }
}