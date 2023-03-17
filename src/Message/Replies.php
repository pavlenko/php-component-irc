<?php

namespace PE\Component\IRC\Message;

class Replies
{
    public const ERR_NOSUCHNICK = 401;
    public const ERR_NOSUCHSERVER = 402;
    public const ERR_NOSUCHCHANNEL = 403;
    public const ERR_CANNOTSENDTOCHAN = 404;
    public const ERR_TOOMANYCHANNELS = 405;
    public const ERR_WASNOSUCHNICK = 406;
    public const ERR_TOOMANYTARGETS = 407;
    public const ERR_NOORIGIN = 409;
    public const ERR_NORECIPIENT = 411;
    public const ERR_NOTEXTTOSEND = 412;
    public const ERR_NOTOPLEVEL = 413;
    public const ERR_WILDTOPLEVEL = 414;
    public const ERR_UNKNOWNCOMMAND = 421;
    public const ERR_NOMOTD = 422;
    public const ERR_NOADMININFO = 423;
    public const ERR_FILEERROR = 424;
    public const ERR_NONICKNAMEGIVEN = 431;
    public const ERR_ERRONEUSNICKNAME = 432;
    public const ERR_NICKNAMEINUSE = 433;
    public const ERR_NICKCOLLISION = 436;
    public const ERR_USERNOTINCHANNEL = 441;
    public const ERR_NOTONCHANNEL = 442;
    public const ERR_USERONCHANNEL = 443;
    public const ERR_NOLOGIN = 444;
    public const ERR_SUMMONDISABLED = 445;
    public const ERR_USERSDISABLED = 446;
    public const ERR_NOTREGISTERED = 451;
    public const ERR_NEEDMOREPARAMS = 461;
    public const ERR_ALREADYREGISTRED = 462;
    public const ERR_NOPERMFORHOST = 463;
    public const ERR_PASSWDMISMATCH = 464;
    public const ERR_YOUREBANNEDCREEP = 465;
    public const ERR_KEYSET = 467;
    public const ERR_CHANNELISFULL = 471;
    public const ERR_UNKNOWNMODE = 472;
    public const ERR_INVITEONLYCHAN = 473;
    public const ERR_BANNEDFROMCHAN = 474;
    public const ERR_BADCHANNELKEY = 475;
    public const ERR_NOPRIVILEGES = 481;
    public const ERR_CHANOPRIVSNEEDED = 482;
    public const ERR_CANTKILLSERVER = 483;
    public const ERR_NOOPERHOST = 491;
    public const ERR_UMODEUNKNOWNFLAG = 501;
    public const ERR_USERSDONTMATCH = 502;
}