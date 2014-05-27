<?php

  class IdTest extends PHPUnit_Framework_TestCase {

    /**
     * ::COOKIE_NAME
     */

    public function testCookieName() {
      $this->assertEquals(Monk\Id::COOKIE_NAME, '_monkIdPayload');
    }

    /**
     * .config
     */

    public function testConfigGet() {
      Helpers::loadConfig();

      $this->assertEquals(Monk\Id::config('app_id'), 'ca13c9d1-6600-490e-a448-adb99e2eb906');
    }

    public function testConfigSet() {
      Helpers::loadConfig();

      Monk\Id::config('app_id', 'set_app_id');

      $this->assertEquals(Monk\Id::config('app_id'), 'set_app_id');
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage no `app_id` config value
     */
    public function testConfigUnsetRequiredValue() {
      Helpers::loadConfig();

      Monk\Id::config('app_id', '');
    }

    public function testConfigWhenConfigIsNotLoaded() {
      Helpers::setConfigEnv();

      $this->assertEquals(Monk\Id::config('app_id'), 'env_app_id');

      Helpers::resetConfigEnv();
    }

    /**
     * .loadConfig
     */

    public function testLoadConfigWhenPathIsSpecified() {
      $this->assertEquals(Monk\Id::loadConfig(Helpers::configFilePath(), Helpers::configEnv()),
                          Helpers::expectedConfigTest());
    }

    public function testLoadConfigWhenPathIsNotSpecified() {
      Helpers::setConfigEnv();

      $this->assertEquals(Monk\Id::loadConfig(null, 'env'), Helpers::expectedConfig('env'));

      Helpers::resetConfigEnv();
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigWhenPathDoesNotExist() {
      Monk\Id::loadConfig('/does/not/exist.ini', Helpers::configEnv());
    }

    public function testLoadConfigWhenEnvironmentIsSpecified() {
      $this->assertEquals(Monk\Id::loadConfig(Helpers::configFilePath(), Helpers::configEnv()),
                          Helpers::expectedConfigTest());
    }

    public function testLoadConfigWhenEnvironmentIsNotSpecified() {
      Helpers::setConfigEnv();

      $this->assertEquals(Monk\Id::loadConfig(Helpers::configFileAltPath(), null), Helpers::expectedConfig('env'));

      Helpers::resetConfigEnv();
    }

    public function testLoadConfigWhenEnvironmentIsNotSpecifiedDefault() {
      $this->assertEquals(Monk\Id::loadConfig(Helpers::configFilePath(), null), Helpers::expectedConfig('development'));
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigWhenEnvironmentDoesNotExist() {
      Monk\Id::loadConfig(Helpers::configFilePath(), 'does_not_exist');
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigWhenConfigIsNotValid() {
      Monk\Id::loadConfig(TESTS_CONFIG_PATH . '/monkIdInvalid.ini', Helpers::configEnv());
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage no `app_secret` config value
     */
    public function testLoadConfigWhenRequiredValueIsNotSet() {
      Monk\Id::loadConfig(Helpers::configFileAltPath(), 'required');
    }

    /**
     * .loadPayload
     */

    public function testLoadPayloadString() {
      Helpers::loadConfig();

      $this->assertEquals(Monk\Id::loadPayload(Helpers::validPayload()), Helpers::expectedPayload());
    }

    public function testLoadPayloadArray() {
      Helpers::loadConfig();
      $cookies = array(Monk\Id::COOKIE_NAME => Helpers::validPayload());

      $this->assertEquals(Monk\Id::loadPayload($cookies), Helpers::expectedPayload());
    }

    public function testLoadPayloadCookie() {
      Helpers::loadConfig();

      $_COOKIE[Monk\Id::COOKIE_NAME] = Helpers::validPayload();

      $this->assertEquals(Monk\Id::loadPayload(null), Helpers::expectedPayload());
    }

    public function testLoadPayloadWhenCannotBeDecoded() {
      Helpers::loadConfig();

      $this->assertEmpty(Monk\Id::loadPayload('cannot be decoded'));
    }

    public function testLoadPayloadWhenCannotBeVerified() {
      Helpers::loadConfig();

      $this->assertEmpty(Monk\Id::loadPayload(Helpers::invalidPayload()));
    }

    public function testLoadPayloadNull() {
      Helpers::loadConfig();

      $this->assertEmpty(Monk\Id::loadPayload(null));
    }

    /**
     * .signedIn
     */

    public function testSignedInWhenSignedIn() {
      Helpers::loadConfig();
      Helpers::loadPayload();

      $this->assertTrue(Monk\Id::signedIn());
    }

    public function testSignedInWhenSignedOut() {
      Helpers::loadConfig();

      $this->assertFalse(Monk\Id::signedIn());
    }

    /**
     * .userEmail
     */

    public function testUserEmailWhenSignedIn() {
      Helpers::loadConfig();
      Helpers::loadPayload();

      $this->assertEquals(Monk\Id::userEmail(), 'jstayton@monkdevelopment.com');
    }

    public function testUserEmailWhenSignedOut() {
      Helpers::loadConfig();

      $this->assertNull(Monk\Id::userEmail());
    }

    /**
     * .userId
     */

    public function testUserIdWhenSignedIn() {
      Helpers::loadConfig();
      Helpers::loadPayload();

      $this->assertEquals(Monk\Id::userId(), '62c988ba-13d8-473e-adeb-8f7d2c62846a');
    }

    public function testUserIdWhenSignedOut() {
      Helpers::loadConfig();

      $this->assertNull(Monk\Id::userId());
    }

  }

?>
