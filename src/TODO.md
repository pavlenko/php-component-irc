# TODO

### protocol functions
```php
<?php
// DTOs
class Channel {
#define PRIVATE		0b000001
#define SECRET		0b000010
#define MODERATED	0b000100
#define INVITEONLY	0b001000
#define TOPICSET	0b010000
#define NOMSGOUT	0b100000
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
public $flags;
public function __construct(User $creator, string $name, string $pass) {
//TODO add to users
//TODO add to operators
//TODO send info
}
public function getName(): string;
public function getPass(): string;
public function setPass(string $pass): void;
public function getTopic(): string;
public function setTopic(string $topic): void;
public function getFlags(): int;
public function setFlag(int $flag): void;
public function clrFlag(int $flag): void;
public function attachUser(User $user): void;
public function detachUser(User $user): void;
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
public $maxInactiveTimeout;
public $maxResponseTimeout;
public $maxChannels;
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