Monk ID PHP
===========

[![Latest Stable Version](https://img.shields.io/packagist/v/monkdev/monk-id.svg?style=flat)](https://packagist.org/packages/monkdev/monk-id)
[![Build Status](https://img.shields.io/travis/MonkDev/monk-id-php/dev.svg?style=flat)](https://travis-ci.org/MonkDev/monk-id-php)
[![codecov](https://codecov.io/gh/MonkDev/monk-id-php/branch/dev/graph/badge.svg)](https://codecov.io/gh/MonkDev/monk-id-php)
[![Dependency Status](https://img.shields.io/gemnasium/MonkDev/monk-id-php.svg?style=flat)](https://gemnasium.com/MonkDev/monk-id-php)

Integrate Monk ID authentication and single sign-on for apps and websites on the
server-side.

*   [Documentation](http://monkdev.github.io/monk-id-php/classes/Monk.Id.html)

Overview
--------

### Install

Using [Composer](http://getcomposer.org), add `monkdev/monk-id` to your
`composer.json`:

```json
{
  "require": {
    "monkdev/monk-id": "~1.0"
  }
}
```

```bash
$ composer update
```

Or:

```bash
$ composer require monkdev/monk-id:~1.0
```

### Configure

Configuration is done in an external INI file. There's a sample file in this
repository: `config/monkId.sample.yml`. Copy this file to your codebase, then
load the config in your code during initialization:

```php
Monk\Id::loadConfig('/path/to/monkId.ini', 'development');
```

Remember, replace the sample values with your own, and keep the file safe as it
contains your app secret.

### Access

If you have Monk ID JS configured to store the payload automatically in a cookie
(the default), you can skip the next part as the cookie is also loaded
automatically.

If not, the encoded payload can be passed directly, which is useful if you're
sending it in a GET/POST request instead:

```php
Monk\Id::loadPayload($monkIdPayload);
```

Loading the payload must be done before trying to access any values stored in
the payload. In an MVC framework, this usually means placing it in a method in
your `ApplicationController` that's executed before the specific action is
processed.

Once the payload is loaded, you can ask whether the user is logged in:

```php
Monk\Id::loggedIn()
```

Or for their ID and email:

```php
Monk\Id::userId()
Monk\Id::userEmail()
```

`null` is returned if the user isn't logged in or the payload can't be decoded
and verified.

Development
-----------

[Composer](http://getcomposer.org) is used for dependency management and task
running. Start by installing the dependencies:

```bash
$ composer install
```

### Tests

Testing is done with [PHPUnit](http://phpunit.de). To run the tests:

```bash
$ composer test
```

Continuous integration is setup through [Travis CI](https://travis-ci.org/MonkDev/monk-id-php)
to run the tests against PHP v5.6, v7.0, and v7.1. ([Circle CI](https://circleci.com/gh/MonkDev/monk-id-php)
is also setup to run the tests against PHP v5.6, but is backup for now until
multiple versions can easily be specified.) The code coverage results are sent
to [Codecov](https://codecov.io/gh/MonkDev/monk-id-php) during CI for tracking
over time. Badges for both are dispayed at the top of this README.

While the test suite is complete, it's not a bad idea to also test changes
manually in real-world integrations.

### Documentation

[phpDocumentor](http://phpdoc.org) is used for code documentation. To build:

```bash
$ composer phpdoc
```

This creates a `doc` directory (that is ignored by git).

### Quality

A number of code quality tools are configured to aid in development. To run them
all at once:

```bash
$ composer quality
```

Each tool can also be run individually:

*   [php -l](http://www.php.net/manual/en/function.php-check-syntax.php):
    `$ composer phplint`
*   [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer):
    `$ composer phpcs`
*   [PHP Copy/Paste Detector](https://github.com/sebastianbergmann/phpcpd):
    `$ composer phpcpd`
*   [PHPLOC](https://github.com/sebastianbergmann/phploc): `$ composer phploc`
*   [PHP Mess Detector](http://phpmd.org): `$ composer phpmd`
*   [SensioLabs Security Checker](https://github.com/sensiolabs/security-checker):
    `$ composer security-checker`

Deployment
----------

Publishing a release to [Packagist](https://packagist.org) simply requires
creating a git tag:

```bash
$ git tag -a vMAJOR.MINOR.PATCH -m "Version MAJOR.MINOR.PATCH"
$ git push origin vMAJOR.MINOR.PATCH
```

Be sure to choose the correct version by following [Semantic Versioning](http://semver.org).

### Publish Documentation

After releasing a new version, the documentation must be manually built and
published to the `gh-pages` branch.
