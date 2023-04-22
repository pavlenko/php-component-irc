<?php

namespace PE\Component\IRC\Protocol;

/**
 * Command representation (stats + handler)
 *
 * @property string $name
 * @property int $count
 * @property int $bytes
 * @property int $count_remote
 * @property callable $handler
 */
interface CommandInterface
{
}
