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
    public const ERR_NICKNAMEINUSE    = 433;//<target> ::= <nick> :Nickname is already in use
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
}