<?php

namespace PE\Component\IRC;

trait HandleChannelCommands
{
    //TODO helpers

    private function handleChannelFlags(CMD $cmd, SessionInterface $sess, ChannelInterface $chan)
    {
        $flag = $cmd->getArg(1);
        if ('o' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                $operator = $this->sessions->searchByName($cmd->getArg(2));
                if (null === $operator) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
                } elseif ('+' === $flag[0]) {
                    $chan->operators()->attach($operator);
                } elseif ('-' === $flag[0]) {
                    $chan->operators()->detach($operator);
                }
            }
        } elseif ('p' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_PRIVATE);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_PRIVATE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_SECRET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_SECRET);
            }
        } elseif ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_INVITE_ONLY);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_INVITE_ONLY);
            }
        } elseif ('t' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_TOPIC_SET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_TOPIC_SET);
            }
        } elseif ('m' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(Channel::FLAG_MODERATED);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(Channel::FLAG_MODERATED);
            }
        } elseif ('l' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setLimit((int) $cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setLimit(0);
                }
            }
        } elseif ('k' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setPass($cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setPass('');
                }
            }
        } else {
            $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
//TODO others
        $ref = <<<'CPP'
std::string    chanName = msg.getParams()[0];
std::string    flag = msg.getParams()[1];
else if (flag == "+n")
{}
else if (flag == "-n")
{}
else if (flag == "+b")
{
    if (msg.getParams().size() < 3)
        return sendError(user, ERR_NEEDMOREPARAMS, msg.getCommand());
    else
        channels[chanName]->addBanMask(msg.getParams()[2]);
}
else if (flag == "-b")
{
    if (msg.getParams().size() < 3)
        return sendError(user, ERR_NEEDMOREPARAMS, msg.getCommand());
    else
        channels[chanName]->removeBanMask(msg.getParams()[2]);
}
else if (flag == "+v")
{
    if (msg.getParams().size() < 3)
        return sendError(user, ERR_NEEDMOREPARAMS, msg.getCommand());
    else if (!containsNickname(msg.getParams()[2]))
        return sendError(user, ERR_NOSUCHNICK, msg.getParams()[2]);
    else
        channels[chanName]->addSpeaker(*(getUserByName(msg.getParams()[2])));
}
else if (flag == "-v")
{
    if (msg.getParams().size() < 3)
        return sendError(user, ERR_NEEDMOREPARAMS, msg.getCommand());
    else if (!containsNickname(msg.getParams()[2]))
        return sendError(user, ERR_NOSUCHNICK, msg.getParams()[2]);
    else
        channels[chanName]->removeSpeaker(*(getUserByName(msg.getParams()[2])));
}
CPP;
    }

    private function handleSessionFlags(CMD $cmd, Connection $conn, SessionInterface $sess)
    {
        $ref = <<<'CPP'
std::string    flag = msg.getParams()[1];
if (flag == "+i")
    user.setFlag(INVISIBLE);
else if (flag == "-i")
    user.removeFlag(INVISIBLE);
else if (flag == "+s")
    user.setFlag(RECEIVENOTICE);
else if (flag == "-s")
    user.removeFlag(RECEIVENOTICE);
else if (flag == "+w")
    user.setFlag(RECEIVEWALLOPS);
else if (flag == "-w")
    user.removeFlag(RECEIVEWALLOPS);
else if (flag == "+o")
{}
else if (flag == "-o")
    user.removeFlag(IRCOPERATOR);
else
    return sendError(user, ERR_UMODEUNKNOWNFLAG);
return 0;
CPP;
    }

    public function handleMODE(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {
        if ($cmd->numArgs() < 1) {
            $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
        } else {
            $name = $cmd->getArg(0);
            if ('#' === $name[0]) {
                $chan = $this->channels->searchByName($name);
                if (null === $chan) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_CHANNEL, [$name]);
                } elseif (!$chan->operators()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_OPERATOR_PRIVILEGES_NEEDED, [$name]);
                } elseif (!$chan->sessions()->searchByName($sess->getNickname())) {
                    $sess->sendERR(ERR::ERR_NOT_ON_CHANNEL, [$name]);
                } elseif ($cmd->numArgs() === 1) {
                    $sess->sendRPL(RPL::RPL_CHANNEL_MODE_IS, [$name, '+' . $chan->getFlagsAsString()]);
                } else {
                    $this->handleChannelFlags($cmd, $sess, $chan);
                    //TODO send message to all channel sessions
                }
            } else {
                //TODO session mode
            }
        }
    }

    public function handleJOIN(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleTOPIC(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleINVITE(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleKICK(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handlePART(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleNAMES(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}

    public function handleLIST(CMD $cmd, Connection $conn, SessionInterface $sess): void
    {}
}