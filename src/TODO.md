# TODO

### protocol functions
```php
<?php
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