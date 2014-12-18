<?php
namespace Tests;

use Tests\Helpers as Helpers;
use \Monk\Id as Id;

class IdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ::COOKIE_NAME
     */

    public function testCookieName()
    {
        $this->assertEquals(Id::COOKIE_NAME, '_monkIdPayload');
    }

    /**
     * .config
     */

    public function testConfigGet()
    {
        Helpers::loadConfig();

        $this->assertEquals(Id::config('app_id'), 'ca13c9d1-6600-490e-a448-adb99e2eb906');
    }

    public function testConfigSet()
    {
        Helpers::loadConfig();

        Id::config('app_id', 'set_app_id');

        $this->assertEquals(Id::config('app_id'), 'set_app_id');
    }

    public function testConfigUnsetRequiredValue()
    {
        Helpers::loadConfig();

        $this->setExpectedException('\Exception', 'no `app_id` config value');

        Id::config('app_id', '');
    }

    public function testConfigWhenConfigIsNotLoaded()
    {
        Helpers::setConfigEnv();

        $this->assertEquals(Id::config('app_id'), 'env_app_id');

        Helpers::resetConfigEnv();
    }

    /**
     * .loadConfig
     */

    public function testLoadConfigWhenPathIsSpecified()
    {
        $this->assertEquals(
            Id::loadConfig(Helpers::configFilePath(), Helpers::configEnv()),
            Helpers::expectedConfigTest()
        );
    }

    public function testLoadConfigWhenPathIsNotSpecified()
    {
        Helpers::setConfigEnv();

        $this->assertEquals(Id::loadConfig(null, 'env'), Helpers::expectedConfig('env'));

        Helpers::resetConfigEnv();
    }

    public function testLoadConfigWhenPathDoesNotExist()
    {
        $this->setExpectedException('\Exception', 'no config loaded');

        Id::loadConfig('/does/not/exist.ini', Helpers::configEnv());
    }

    public function testLoadConfigWhenEnvironmentIsSpecified()
    {
        $this->assertEquals(
            Id::loadConfig(Helpers::configFilePath(), Helpers::configEnv()),
            Helpers::expectedConfigTest()
        );
    }

    public function testLoadConfigWhenEnvironmentIsNotSpecified()
    {
        Helpers::setConfigEnv();

        $this->assertEquals(Id::loadConfig(Helpers::configFileAltPath(), null), Helpers::expectedConfig('env'));

        Helpers::resetConfigEnv();
    }

    public function testLoadConfigWhenEnvironmentIsNotSpecifiedDefault()
    {
        $this->assertEquals(Id::loadConfig(Helpers::configFilePath(), null), Helpers::expectedConfig('development'));
    }

    public function testLoadConfigWhenEnvironmentDoesNotExist()
    {
        $this->setExpectedException('\Exception', 'no config loaded');

        Id::loadConfig(Helpers::configFilePath(), 'does_not_exist');
    }

    public function testLoadConfigWhenConfigIsNotValid()
    {
        $this->setExpectedException('\Exception', 'no config loaded');

        Id::loadConfig(TESTS_CONFIG_PATH . DS . 'monkIdInvalid.ini', Helpers::configEnv());
    }

    public function testLoadConfigWhenRequiredValueIsNotSet()
    {
        $this->setExpectedException('\Exception', 'no `app_secret` config value');

        Id::loadConfig(Helpers::configFileAltPath(), 'required');
    }

    /**
     * .loadPayload
     */

    public function testLoadPayloadString()
    {
        Helpers::loadConfig();

        $this->assertEquals(Id::loadPayload(Helpers::validPayload()), Helpers::expectedPayload());
    }

    public function testLoadPayloadArray()
    {
        Helpers::loadConfig();
        $cookies = array(Id::COOKIE_NAME => Helpers::validPayload());

        $this->assertEquals(Id::loadPayload($cookies), Helpers::expectedPayload());
    }

    public function testLoadPayloadCookie()
    {
        Helpers::loadConfig();

        $_COOKIE[Id::COOKIE_NAME] = Helpers::validPayload();

        $this->assertEquals(Id::loadPayload(null), Helpers::expectedPayload());
    }

    public function testLoadPayloadWhenCannotBeDecoded()
    {
        Helpers::loadConfig();

        $this->assertEmpty(Id::loadPayload('cannot be decoded'));
    }

    public function testLoadPayloadWhenCannotBeVerified()
    {
        Helpers::loadConfig();

        $this->assertEmpty(Id::loadPayload(Helpers::invalidPayload()));
    }

    public function testLoadPayloadNull()
    {
        Helpers::loadConfig();

        $this->assertEmpty(Id::loadPayload(null));
    }

    /**
     * .signedIn
     */

    public function testSignedInWhenSignedIn()
    {
        Helpers::loadConfig();
        Helpers::loadPayload();

        $this->assertTrue(Id::signedIn());
    }

    public function testSignedInWhenSignedOut()
    {
        Helpers::loadConfig();

        $this->assertFalse(Id::signedIn());
    }

    /**
     * .userEmail
     */

    public function testUserEmailWhenSignedIn()
    {
        Helpers::loadConfig();
        Helpers::loadPayload();

        $this->assertEquals(Id::userEmail(), 'jstayton@monkdevelopment.com');
    }

    public function testUserEmailWhenSignedOut()
    {
        Helpers::loadConfig();

        $this->assertNull(Id::userEmail());
    }

    /**
     * .userId
     */

    public function testUserIdWhenSignedIn()
    {
        Helpers::loadConfig();
        Helpers::loadPayload();

        $this->assertEquals(Id::userId(), '62c988ba-13d8-473e-adeb-8f7d2c62846a');
    }

    public function testUserIdWhenSignedOut()
    {
        Helpers::loadConfig();

        $this->assertNull(Id::userId());
    }
}
