# TODO

- Differentiate handlers???
- Add roles: Server/Client
- Add reply handlers???
- Add server requests for allow forward replies???

```php
// Create client instance with auth params & helper classes
$logger = new Logger();
$factory = new Factory($logger);
$config = new Config($nickname, $username, $realname, $password);
$client = new Client($factory, $config);

// Connect to server
$client->connect('tls://irc.libera.chat:6697')
    ->then(function (RPL $rpl) use ($client) {
        // Reply is WELCOME, other replies is handled by attached handlers
        // If success connect here you can add command/reply listeners and get some info from server (if allowed)
        $client->addCommandHahdler('PRIVMSG', function ($message) use ($client) {
            if ('PING' === $message) {
                $client->sendCMD('PRIVMSG', 'AAA');// Reply to message
            }
        });
        
        $client->sendCMD('LIST');// List all visible channels, if not auto received
        $client->sendCMD('NAMES');// List all visible users, if not auto received
        $client->sendCMD('JOIN');// Join some channels for allow send to channel users, or create one
    })
    ->else(function (ERR $err) {
        echo $err->toLogger() . "\n";
        $client->exit();
    });

// Start dispatch responses
$client->wait();
```