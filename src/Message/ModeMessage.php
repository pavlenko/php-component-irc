<?php

namespace PE\Component\IRC\Message;

/**
 * MODE <channel> {[+|-]|o|p|s|i|t|n|b|v} [<limit>] [<user>] [<ban mask>]
 * MODE <nickname> {[+|-]|i|w|s|o}
 *
 * ERR_NEEDMOREPARAMS
 * ERR_CHANOPRIVSNEEDED
 * ERR_NOSUCHNICK
 * ERR_NOTONCHANNEL
 * ERR_KEYSET
 * ERR_UNKNOWNMODE
 * ERR_NOSUCHCHANNEL
 * ERR_USERSDONTMATCH
 * ERR_UMODEUNKNOWNFLAG
 * RPL_CHANNELMODEIS
 * RPL_BANLIST
 * RPL_ENDOFBANLIST
 * RPL_UMODEIS
 */
class ModeMessage
{

}