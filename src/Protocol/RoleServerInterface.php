<?php

namespace PE\Component\IRC\Protocol;

/**
 * Server representation
 *
 * <code>
 * -> CMD(PASS $password $version $flags [$options])
 * -> CMD(SERVER $name $hop_count $server_id :$info)
 * <- PASS
 * <- SERVER
 * </code>
 *
 * @property string $version
 * @property string $flags
 * @property string $options
 * @property string $name
 * @property string $info
 * @property int $server_id
 * @property int $hop_count
 *
 * @property $session
 */
interface RoleServerInterface
{
}
