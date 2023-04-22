<?php

namespace PE\Component\IRC\Protocol;

/**
 * Service role representation
 *
 * <code>
 * -> CMD(PASS $password)
 * -> CMD(SERVICE $name * $mask 0 0 :$info)
 * <- RPL(YOU_ARE_SERVICE)
 * </code>
 *
 * @property string $name
 * @property string $mask
 * @property string $info
 *
 * @property $session
 * @property $handler
 */
interface RoleServiceInterface
{
}
