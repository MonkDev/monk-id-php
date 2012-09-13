MonkId
======

PHP client for [Monk ID](http://monkid.com).

*   [Documentation](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html)
*   [Release Notes](https://github.com/monkdev/monk-id-client-php/wiki/Release-Notes)

Requirements
------------

*   PHP >= 5.2.0
*   [cURL support](http://us.php.net/manual/en/curl.setup.php) (usually
    compiled with PHP by default)

Installation
------------

Copy _MonkId.php_ to your codebase (perhaps to the _vendor_ directory) and add
to your autoloader or `require` directly. Then copy _monkid.sample.ini_ to your
_config_ directory and add your API key, saving the file as _monkid.ini_. See
below on loading this config file during your app's bootstrapping.

Usage
-----

`MonkId` cannot be instantiated — all methods are class (static) methods.

### Config

To begin, a number of config values must be set to make API requests. The
easiest way of doing this is to load an ini config file:

    MonkId::loadConfig('/path/to/monkid.ini', 'development');

This file should follow the same syntax as your _php.ini_ and contain the
following values:

    [development]
    api_key = {YOUR API KEY}
    host = "id-dev.monkdev.com"
    port = 443
    ssl = On

    [production]
    api_key = {YOUR API KEY}
    host = "id.monkdev.com"
    ssl = On

There are two different sets of config values here, each for a different
environment. The second parameter to `loadConfig` specifies which
environment to load.

Config values can also be set manually, one-by-one:

    MonkId::setConfig('key', 'value');

Finally, a config value can be retrieved:

    MonkId::getConfig('key');

These config values are used for all API requests.

### API Requests

All API request methods return the JSON response decoded into an associative
array:

    array(4) {
      ["success"]=>
      bool(true)
      ["user"]=>
      array(12) {
        ["authentication_token"]=>
        string(20) "9LZyw8bJspoWTqCpg6wv"
        ["email"]=>
        string(28) "st.anthony@thegreat.org"
        ["logged_out_at"]=>
        string(20) "2012-09-13T19:21:04Z"
        ["confirmed_at"]=>
        string(20) "2012-09-12T23:51:18Z"
        ["last_sign_in_at"]=>
        string(20) "2012-09-12T23:51:18Z"
        ["signed_in"]=>
        bool(false)
        ["one_time_token"]=>
        string(40) "a70a586d526b0ae08e9d63fa5d5d7ec908c64481"
      }
      ["status_code"]=>
      int(0)
      ["status_key"]=>
      string(7) "success"
    }

If the request is not successful, the response does not contain the `user`
values:

    array(3) {
      ["success"]=>
      bool(false)
      ["status_key"]=>
      string(12) "email_exists"
      ["status_code"]=>
      int(2)
    }

The `MonkId` class contains constants for all of the field names and status
codes, such as `MonkId::EMAIL` and `MonkId::STATUS_EMAIL_EXISTS`.

#### [Register](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#register)

    MonkId::register('st.anthony@thegreat.org', 'password');

#### [Update](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#update)

    MonkId::update('st.anthony@thegreat.org', 'password', 'authToken');

#### [Send Password Reset Email](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#passwordReset)

    MonkId::passwordReset('st.anthony@thegreat.org');

#### [Log In](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#logIn)

    MonkId::logIn('st.anthony@thegreat.org', 'password');

#### [Log Out](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#logOut)

    MonkId::logOut('authToken');

#### [Log In Status](http://monkdev.github.com/monk-id-client-php/classes/MonkId.html#status)

    MonkId::status('authToken');

Feedback
--------

Please open an issue to request a feature or submit a bug report. Code
contributions are also welcome.

1.  Fork it
2.  Create your feature branch (`git checkout -b my-new-feature`)
3.  Commit your changes (`git commit -am 'Added some feature'`)
4.  Push to the branch (`git push origin my-new-feature`)
5.  Create new Pull Request
