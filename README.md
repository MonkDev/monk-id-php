Monk ID PHP
===========

Copy `lib/Monk/Id.php` and `config/monkid.sample.ini` to your app.

Load your config:

```php
Monk\Id::loadConfig('/path/to/monkid.ini', 'development')
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
