<?php

namespace PE\Component\IRC\Message;

class Replies
{
    public const ERR_NOSUCHNICK       = 401;//<target> ::= <nickname> :No such nick/channel
    public const ERR_NOSUCHSERVER     = 402;//<target> ::= <server name> :No such server
    public const ERR_NOSUCHCHANNEL    = 403;//<target> ::= <channel name> :No such channel
    public const ERR_CANNOTSENDTOCHAN = 404;//<target> ::= <channel name> :Cannot send to channel
    public const ERR_TOOMANYCHANNELS  = 405;//<target> ::= <channel name> :You have joined too many channels
    public const ERR_WASNOSUCHNICK    = 406;//<target> ::= <nickname> :There was no such nickname
    public const ERR_TOOMANYTARGETS   = 407;//<target> ::= <target> :Duplicate recipients. No message delivered
    public const ERR_NOORIGIN         = 409;//<target> ::= :No origin specified
    public const ERR_NORECIPIENT      = 411;//<target> ::= :No recipient given (<command>)
    public const ERR_NOTEXTTOSEND     = 412;//<target> ::= :No text to send
    public const ERR_NOTOPLEVEL       = 413;//<target> ::= <mask> :No toplevel domain specified
    public const ERR_WILDTOPLEVEL     = 414;//<target> ::= <mask> :Wildcard in toplevel domain
    public const ERR_UNKNOWNCOMMAND   = 421;//<target> ::= <command> :Unknown command
    public const ERR_NOMOTD           = 422;//<target> ::= :MOTD File is missing
    public const ERR_NOADMININFO      = 423;//<target> ::= <server> :No administrative info available
    public const ERR_FILEERROR        = 424;//<target> ::= :File error doing <file op> on <file>
    public const ERR_NONICKNAMEGIVEN  = 431;//<target> ::= :No nickname given
    public const ERR_ERRONEUSNICKNAME = 432;//<target> ::= <nick> :Erroneous nickname
    public const ERR_NICKNAMEINUSE    = 433;//<target> ::= "<nick> :Nickname is already in use
    public const ERR_NICKCOLLISION    = 436;//<target> ::= <nick> :Nickname collision KILL
    public const ERR_USERNOTINCHANNEL = 441;//<target> ::= <nick> <channel> :They aren’t on that channel
    public const ERR_NOTONCHANNEL     = 442;//<target> ::= <channel> :You’re not on that channel
    public const ERR_USERONCHANNEL    = 443;//<target> ::= <user> <channel> :is already on channel
    public const ERR_NOLOGIN          = 444;//<target> ::= <user> :User not logged in
    public const ERR_SUMMONDISABLED   = 445;//<target> ::= :SUMMON has been disabled
    public const ERR_USERSDISABLED    = 446;//<target> ::= :USERS has been disabled
    public const ERR_NOTREGISTERED    = 451;//<target> ::= :You have not registered
    public const ERR_NEEDMOREPARAMS   = 461;//<target> ::= <command> :Not enough parameters
    public const ERR_ALREADYREGISTRED = 462;//<target> ::= :You may not re-register
    public const ERR_NOPERMFORHOST    = 463;//<target> ::= :Your host isn’t among the privileged
    public const ERR_PASSWDMISMATCH   = 464;//<target> ::= :Password incorrect
    public const ERR_YOUREBANNEDCREEP = 465;//<target> ::= :You are banned from this server
    public const ERR_KEYSET           = 467;//<target> ::= <channel> :Channel key already set
    public const ERR_CHANNELISFULL    = 471;//<target> ::= <channel> :Cannot join channel (+l)
    public const ERR_UNKNOWNMODE      = 472;//<target> ::= <char> :is unknown mode char to me
    public const ERR_INVITEONLYCHAN   = 473;//<target> ::= <channel> :Cannot join channel (+i)
    public const ERR_BANNEDFROMCHAN   = 474;//<target> ::= <channel> :Cannot join channel (+b)
    public const ERR_BADCHANNELKEY    = 475;//<target> ::= <channel> :Cannot join channel (+k)
    public const ERR_NOPRIVILEGES     = 481;//<target> ::= :Permission Denied- You’re not an IRC operator
    public const ERR_CHANOPRIVSNEEDED = 482;//<target> ::= <channel> :You’re not channel operator
    public const ERR_CANTKILLSERVER   = 483;//<target> ::= :You cant kill a server!
    public const ERR_NOOPERHOST       = 491;//<target> ::= :No O-lines for your host
    public const ERR_UMODEUNKNOWNFLAG = 501;//<target> ::= :Unknown MODE flag
    public const ERR_USERSDONTMATCH   = 502;//<target> ::= :Cant change mode for other users

    public const RPL_NONE            = 300;//<target> ::=
    public const RPL_USERHOST        = 302;//<target> ::=
    public const RPL_ISON            = 303;//<target> ::=
    public const RPL_AWAY            = 301;//<target> ::=
    public const RPL_UNAWAY          = 305;//<target> ::=
    public const RPL_NOWAWAY         = 306;//<target> ::=
    public const RPL_WHOISUSER       = 311;//<target> ::=
    public const RPL_WHOISSERVER     = 312;//<target> ::=
    public const RPL_WHOISOPERATOR   = 313;//<target> ::=
    public const RPL_WHOISIDLE       = 317;//<target> ::=
    public const RPL_ENDOFWHOIS      = 318;//<target> ::=
    public const RPL_WHOISCHANNELS   = 319;//<target> ::=
    public const RPL_WHOWASUSER      = 314;//<target> ::=
    public const RPL_ENDOFWHOWAS     = 369;//<target> ::=
    public const RPL_LISTSTART       = 321;//<target> ::=
    public const RPL_LIST            = 322;//<target> ::=
    public const RPL_LISTEND         = 323;//<target> ::=
    public const RPL_CHANNELMODEIS   = 324;//<target> ::=
    public const RPL_NOTOPIC         = 331;//<target> ::=
    public const RPL_TOPIC           = 332;//<target> ::=
    public const RPL_INVITING        = 341;//<target> ::=
    public const RPL_SUMMONING       = 342;//<target> ::=
    public const RPL_VERSION         = 351;//<target> ::=
    public const RPL_WHOREPLY        = 352;//<target> ::=
    public const RPL_ENDOFWHO        = 315;//<target> ::=
    public const RPL_NAMREPLY        = 353;//<target> ::=
    public const RPL_ENDOFNAMES      = 366;//<target> ::=
    public const RPL_LINKS           = 364;//<target> ::=
    public const RPL_ENDOFLINKS      = 365;//<target> ::=
    public const RPL_BANLIST         = 367;//<target> ::=
    public const RPL_ENDOFBANLIST    = 368;//<target> ::=
    public const RPL_INFO            = 371;//<target> ::=
    public const RPL_ENDOFINFO       = 374;//<target> ::=
    public const RPL_MOTDSTART       = 375;//<target> ::=
    public const RPL_MOTD            = 372;//<target> ::=
    public const RPL_ENDOFMOTD       = 376;//<target> ::=
    public const RPL_YOUREOPER       = 381;//<target> ::=
    public const RPL_REHASHING       = 382;//<target> ::=
    public const RPL_TIME            = 391;//<target> ::=
    public const RPL_USERSSTART      = 392;//<target> ::=
    public const RPL_USERS           = 393;//<target> ::=
    public const RPL_ENDOFUSERS      = 394;//<target> ::=
    public const RPL_NOUSERS         = 395;//<target> ::=
    public const RPL_TRACELINK       = 200;//<target> ::=
    public const RPL_TRACECONNECTING = 201;//<target> ::=
    public const RPL_TRACEHANDSHAKE  = 202;//<target> ::=
    public const RPL_TRACEUNKNOWN    = 203;//<target> ::=
    public const RPL_TRACEOPERATOR   = 204;//<target> ::=
    public const RPL_TRACEUSER       = 205;//<target> ::=
    public const RPL_TRACESERVER     = 206;//<target> ::=
    public const RPL_TRACENEWTYPE    = 208;//<target> ::=
    public const RPL_TRACELOG        = 261;//<target> ::=
    public const RPL_STATSLINKINFO   = 211;//<target> ::=
    public const RPL_STATSCOMMANDS   = 212;//<target> ::=
    public const RPL_STATSCLINE      = 213;//<target> ::=
    public const RPL_STATSNLINE      = 214;//<target> ::=
    public const RPL_STATSILINE      = 215;//<target> ::=
    public const RPL_STATSKLINE      = 216;//<target> ::=
    public const RPL_STATSYLINE      = 218;//<target> ::=
    public const RPL_ENDOFSTATS      = 219;//<target> ::=
    public const RPL_STATSLLINE      = 241;//<target> ::=
    public const RPL_STATSUPTIME     = 242;//<target> ::=
    public const RPL_STATSOLINE      = 243;//<target> ::=
    public const RPL_STATSHLINE      = 244;//<target> ::=
    public const RPL_UMODEIS         = 221;//<target> ::=
    public const RPL_LUSERCLIENT     = 251;//<target> ::=
    public const RPL_LUSEROP         = 252;//<target> ::=
    public const RPL_LUSERUNKNOWN    = 253;//<target> ::=
    public const RPL_LUSERCHANNELS   = 254;//<target> ::=
    public const RPL_LUSERME         = 255;//<target> ::=
    public const RPL_ADMINME         = 256;//<target> ::=
    public const RPL_ADMINLOC1       = 257;//<target> ::=
    public const RPL_ADMINLOC2       = 258;//<target> ::=
    public const RPL_ADMINEMAIL      = 259;//<target> ::=
}