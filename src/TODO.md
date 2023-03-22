# TODO

### protocol functions
```php
<?php
// DTOs
class Channel {
/* @var sring */
public $name;
/* @var string */
public $pass;
/* @var User */
public $creator;
/* @var User[] */
public $users = [];
/* @var User[] */
public $operators = [];
/* @var string */
public $topic;
/* @var int */
public $flags
}
class User {
public $password;
public $username;
public $realname;
public $nickname;
public $servername;
public $flags;
public $channels;
}
class Server {
public $name;
public $port;
public $users;
public $password;
public $info;// for info cmd
public $describe;// for info cmd multiline, why???
public $version;
public $debugLevel;
public $adminInfo;
public $channels;
public $users;
public $motd;
public $nickHistory;//for who was cmd
//TODO timeouts???
}


// If this commands in server - auto-set all vars as possible

public function receive(?string $prefix, int|string $code, array $args = [], string $comment = null): void
{}

public function sendCMD(string $code, array $args = [], string $comment = null, string $prefix): void
{}

public function sendERR(string $servername, int $code, array $args = [], string $comment = null): void
{}

public function sendRPL(string $servername, int $code, array $args = [], string $comment = null): void
{}
```