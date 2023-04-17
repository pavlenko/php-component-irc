<?php

namespace PE\Component\IRC\Handler;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\RPL;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\StorageInterface;

/**
 * <code>
 * STATS [<query> [<server>]]
 *
 * Queries:
 * c - returns a list of servers which the server may connect to or allow connections from;
 * h - returns a list of servers which are either forced to be treated as leaves or allowed to act as hubs;
 * i - returns a list of hosts which the server allows a client to connect from;
 * k - returns a list of banned username/hostname combinations for that server;
 * l - returns a list of the server’s connections, showing how long each connection has been established
 *     and the traffic over that connection in bytes and messages for each direction;
 * m - returns a list of commands supported by the server and the usage count for each if the usage count is non-zero;
 * o - returns a list of hosts from which normal clients may become operators;
 * y - show Y (Class) lines from server’s configuration file;
 * u - returns a string showing how long the server has been up;
 * </code>
 */
class HandlerSTATS implements HandlerInterface
{
    public function __invoke(CMD $cmd, SessionInterface $sess, StorageInterface $stor): int
    {
        if ($cmd->numArgs() === 2) {
            $serv = $stor->sessions()->searchByName($cmd->getArg(1));
            if (null === $serv) {
                return $sess->sendERR(ERR::ERR_NO_SUCH_SERVER, [$sess->getNickname(), $cmd->getArg(0)]);
            }
            return $serv->sendCMD($cmd->getCode(), [$cmd->getArg(0)]);//TODO how to handle response to $sess
        }

        $isOperator = $sess->hasFlag(SessionInterface::FLAG_IRC_OPERATOR);

        $query = $cmd->getArg(0);
        if ($isOperator || 'c' === $query) {
            if (!$isOperator) {
                $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
            } else {
                foreach ($stor->conf('servers') as $serv) {
                    if ($serv['role'] === 'hub') {//TODO flag
                        continue;
                    }
                    //C <host> * <name> <port> <class>
                    $sess->sendRPL(RPL::RPL_STATS_C_LINE, [
                        'C',
                        $serv['host'],
                        '*',
                        $serv['name'],
                        $serv['port'],
                        $serv['class']
                    ]);
                }
            }
        }
        if (null === $query || 'h' === $query) {
            foreach ($stor->conf('servers') as $serv) {
                if ($serv['role'] !== 'hub') {//TODO flag
                    continue;
                }
                //H <host mask> * <servername>
                $sess->sendRPL(RPL::RPL_STATS_H_LINE, ['H', $serv['host'], '*', $serv['name']]);
            }
        }
        if ($isOperator || 'i' === $query) {
            if (!$isOperator) {
                $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
            } else {
                foreach ($stor->conf('servers') as $serv) {
                    if (!$serv['allowed_from']) {//TODO flag
                        continue;
                    }
                    //I <host> * <host> <port> <class>
                    $sess->sendRPL(RPL::RPL_STATS_I_LINE, [
                        'I',
                        $serv['host'],
                        '*',
                        $serv['name'],
                        $serv['port'],
                        $serv['class']
                    ]);
                }
            }
        }
        if (null === $query || 'k' === $query) {
            //K <host> * <username> <port> <class>
            foreach ($stor->channels() as $channel) {
                foreach ($channel->getBanMasks() as $mask) {
                    foreach ($stor->sessions() as $user) {
                        if ($stor->isEqualToRegex($mask, $sess->getPrefix())) {
                            $sess->sendRPL(RPL::RPL_STATS_K_LINE, [
                                'I',
                                $user->getHostname(),
                                '*',
                                $user->getUsername(),
                                parse_url($user->getHostname(), PHP_URL_HOST) ?: 6667,
                                /*TODO class???*/
                            ]);
                        }
                    }
                }
            }
        }
        if (null === $query || 'l' === $query) {
            foreach ($stor->sessions(1) as $serv) {
                $serv->getRegistrationTime();
                //<link name> <send q> <sent messages> <sent bytes> <received messages> <received bytes> <time open>
                $sess->sendRPL(RPL::RPL_STATS_LINK_INFO, [
                    $serv->getServername(),//TODO master_[~master@46.98.139.136]
                    0,
                    $serv->get('sent_num'),
                    $serv->get('sent_bytes'),
                    $serv->get('recv_num'),
                    $serv->get('recv_bytes'),
                    time() - $serv->getRegistrationTime(),
                ]);
            }
        }
        if (null === $query || 'm' === $query) {
            foreach ($stor->commands() as $stat) {
                //<command> <count> [<num> [<num>]] <-- what is nums in reply from libera???
                $sess->sendRPL(RPL::RPL_STATS_COMMANDS, [
                    $stat['name'],
                    $stat['count'],
                ]);
            }
        }
        if ($isOperator || 'o' === $query) {
            if (!$isOperator) {
                $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
            } else {
                foreach ($stor->conf('servers') as $serv) {
                    if (empty($serv['can_operate'])) {//TODO flag
                        continue;
                    }
                    //O <host mask> * <name>
                    $sess->sendRPL(RPL::RPL_STATS_O_LINE, ['O', $serv['host'], '*', $serv['name']]);
                }
            }
        }
        if ($isOperator || 'y' === $query) {
            if (!$isOperator) {
                $sess->sendERR(ERR::ERR_NO_PRIVILEGES);
            } else {
                $class = $stor->conf('class');
                foreach ($class as $line) {
                    //Y <class> <ping frequency> <connect frequency> <max send q>
                    $sess->sendRPL(RPL::RPL_STATS_Y_LINE, [
                        'Y',
                        $line['class'],
                        $line['ping_f'],
                        $line['connect_f'],
                        $line['max_send_q'],
                    ]);
                }
            }
        }
        if (null === $query || 'u' === $query) {
            $interval = (new \DateTime())->diff($stor->getStartedAt(), true);
            $sess->sendRPL(RPL::RPL_STATS_UPTIME, [], $interval->format('Server Up %a days %H:%I:%S'));
        }
        return $sess->sendRPL(RPL::RPL_END_OF_STATS, [$query]);
    }
}
