<?php

namespace PE\Component\IRC\Protocol;

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
 * @property array $channels
 *
 * @property string $awayMessage
 * @property string $quitMessage
 */
interface RoleClientInterface
{
}
