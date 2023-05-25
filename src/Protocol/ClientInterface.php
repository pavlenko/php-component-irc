<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\Deferred;

/**
 * User representation
 *
 * <code>
 * Registration:
 * -> CMD(PASS $password)
 * -> CMD(NICK $nickname)
 * -> CMD(USER $username $mode * :$realname)
 * <- RPL(WELCOME)
 * </code>
 *
 * @property string $nickname
 * @property string $username
 * @property string $realname
 * @property int $mode
 *
 * @property $session
 * @property array $channels
 *
 * @property string $awayMessage
 * @property string $quitMessage
 */
interface ClientInterface
{
    public function connect(string $uri): Deferred;

    public function wait(): void;

    public function exit(): void;
}
