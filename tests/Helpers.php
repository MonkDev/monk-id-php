<?php

  class Helpers {

    public static function configFilePath() {
      return TESTS_CONFIG_PATH . DS . 'monkId.ini';
    }

    public static function configFileAltPath() {
      return TESTS_CONFIG_PATH . DS . 'monkIdAlt.ini';
    }

    public static function validPayload() {
      return 'eyJ1c2VyIjp7ImVtYWlsIjoianN0YXl0b25AbW9ua2RldmVsb3BtZW50LmNvbSIsImlkIjoiNjJjOTg4YmEtMTNkOC00NzNlLWFkZWItOGY3ZDJjNjI4NDZhIiwic2lnbmF0dXJlIjoiOWlGYStLWHlTZTEvS29uM0hXRitLZlRQVDJ2MVl3QyttVEFBQko0QXpsRWZkNmR0UG1HWWpVend2OUtYXG5vbXJreWFMQi9oQjcrWExHQW41OTlLKzlFdz09XG4ifX0=';
    }

    public static function expectedPayload() {
      return array(
        'user' => array(
          'id'        => '62c988ba-13d8-473e-adeb-8f7d2c62846a',
          'email'     => 'jstayton@monkdevelopment.com',
          'signature' => "9iFa+KXySe1/Kon3HWF+KfTPT2v1YwC+mTAABJ4AzlEfd6dtPmGYjUzwv9KX\nomrkyaLB/hB7+XLGAn599K+9Ew==\n"
        )
      );
    }

    public static function invalidPayload() {
      return 'eyJ1c2VyIjp7ImVtYWlsIjoianN0YXl0b25AbW9ua2RldmVsb3BtZW50LmNvbSIsImlkIjoiNjJjOTg4YmEtMTNkOC00NzNlLWFkZWItOGY3ZDJjNjI4NDZhIiwic2lnbmF0dXJlIjoiUlRGcXhIK3dPbzh4V0JGQko0cTNTRnVSc3VOTWxUTE5iak1wTjBFclYxNzh0U3pwS2VlU2J2T29SQzNUXG4zVTkxVCtLK3FQc3JoMjVycEN5QVMrYlFEdz09XG4ifX0=';
    }

    public static function loadPayload() {
      return Monk\Id::loadPayload(self::validPayload());
    }

    public static function configEnv() {
      return 'test';
    }

    public static function loadConfig() {
      return Monk\Id::loadConfig(self::configFilePath(), self::configEnv());
    }

    public static function expectedConfig($environment) {
      return array(
        'app_id'     => "{$environment}_app_id",
        'app_secret' => "{$environment}_app_secret"
      );
    }

    public static function expectedConfigTest() {
      return array(
        'app_id'     => 'ca13c9d1-6600-490e-a448-adb99e2eb906',
        'app_secret' => '98d7ac3f9e22e52f9f23b83ca791db055acad39a27e17dc7'
      );
    }

    public static function setConfigEnv() {
      putenv('MONK_ID_CONFIG=' . self::configFileAltPath());
      putenv('MONK_ID_ENV=env');

      return true;
    }

    public static function resetConfigEnv() {
      putenv('MONK_ID_CONFIG');
      putenv('MONK_ID_ENV');

      return true;
    }

  }

?>
