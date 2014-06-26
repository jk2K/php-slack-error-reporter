php-slack-error-reporter
========================

Report errors directly to slack for quick fixes.

> We use this only on our production environments. By the time the code gets deployed, in theory, it should
never throw any errors or exceptions. When it does, it's important enough that we instantly get notified.

![](doc/example.png)


# Installation

Install via composer. Installation help and versions at [Packagist](https://packagist.org/packages/tailored-tunes/php-slack-error-reporter)

# Usage

Once you create the ```SlackErrorReporter```, it will register an error handler and an exception handler.
Those will take care of everything popping up to them.


```php

use TailoredTunes\SlackNotifier;
use TailoredTunes\SlackErrorReporter;

$slackWebhookUrl = "http://team.slack.com/whatever";

$slack = new SlackNotifier($slackWebhookUrl);
$usernameForMessage = $_SERVER["HTTP_HOST"];
new SlackErrorReporter($slack, $usernameForMessage, '#errors', '#exceptions');

```
