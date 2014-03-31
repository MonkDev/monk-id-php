Monk ID PHP
===========

[![Latest Stable Version](https://poser.pugx.org/monkdev/monk-id/v/stable.png)](https://packagist.org/packages/monkdev/monk-id)
[![Total Downloads](https://poser.pugx.org/monkdev/monk-id/downloads.png)](https://packagist.org/packages/monkdev/monk-id)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/MonkDev/monk-id-php/badges/quality-score.png?s=7bb86d012d75c7911d9d7bd1c7706cfe811e5d68)](https://scrutinizer-ci.com/g/MonkDev/monk-id-php/)

Full API docs: http://monkdev.github.io/monk-id-php/classes/Monk.Id.html

Copy `lib/Monk/Id.php` and `config/monkId.sample.ini` to your app.

Load your config:

```php
Monk\Id::loadConfig('/path/to/monkId.ini', 'development');
```

Next, if you're *not* using the `cookie` option, load an encoded payload:

```php
Monk\Id::loadPayload($encodedPayload);
```

Then you can access the user's ID and email:

```php
Monk\Id::userId()
Monk\Id::userEmail()
```

`null` is returned if the user isn't signed in or the payload can't be decoded
and verified.
