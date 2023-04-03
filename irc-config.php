<?php

use PE\Component\IRC\Config2;

return [
    Config2::CFG_SERVERNAME      => 'local.dev',
    Config2::CFG_ADMIN_LOCATION1 => 'test',
    Config2::CFG_ADMIN_LOCATION2 => 'test',
    Config2::CFG_ADMIN_EMAIL     => 'test@local.dev',
    Config2::CFG_VERSION_NUMBER  => 'v1.0',
    Config2::CFG_VERSION_DEBUG   => 'dev',
    Config2::CFG_VERSION_COMMENT => 'testing',
    Config2::CFG_CREATED_AT      => date(Config2::DEFAULT_DATETIME_FORMAT),
    Config2::CFG_PASSWORD        => null,
    Config2::CFG_MOTD_FILE       => __DIR__ . '/IRCat.motd',
    Config2::CFG_INFO            => '',
    Config2::CFG_OPERATORS       => ['master' => hash('sha256', 'master')]
];