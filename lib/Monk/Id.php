<?php

  namespace Monk;

  class Id {

    const COOKIE_NAME = '_monkIdPayload';

    private static $config = array();
    private static $payload;

    private function __construct() { }

    public static function loadConfig($path, $environment = 'development') {
      $config = parse_ini_file($path, true);

      if (isset($config[$environment])) {
        self::$config = $config[$environment];
      }
      else {
        self::$config = array();
      }

      return self::$config;
    }

    public static function setConfig($key, $value) {
      self::$config[$key] = $value;

      return self;
    }

    public static function getConfig($key) {
      return isset(self::$config[$key]) ? self::$config[$key] : null;
    }

    private static function cookiePayload() {
      return isset($_COOKIE[self::COOKIE_NAME]) ? $_COOKIE[self::COOKIE_NAME] : null;
    }

    private static function decodePayload($encodedPayload) {
      return json_decode(base64_decode($encodedPayload), true);
    }

    private static function expectedSignature($payload) {
      unset($payload['user']['signature']);

      return hash_hmac('sha512', json_encode($payload['user']), self::getConfig('app_secret'), true);
    }

    private static function verifyPayload($payload) {
      $signature = base64_decode($payload['user']['signature']);

      return $signature == self::expectedSignature($payload);
    }

    public static function loadPayload($encodedPayload = null) {
      if ($encodedPayload) {
        if (is_array($encodedPayload)) {
          $payload = $encodedPayload[self::COOKIE_NAME];
        }
        else {
          $payload = $encodedPayload;
        }
      }
      else {
        $payload = self::cookiePayload();
      }

      if (!$payload) {
        return self::$payload = array();
      }

      $payload = self::decodePayload($payload);
      $verified = self::verifyPayload($payload);

      return self::$payload = $verified ? $payload : array();
    }

    private static function payload() {
      if (!isset(self::$payload)) {
        self::loadPayload();
      }

      return self::$payload;
    }

    private static function payloadUser($key) {
      $payload = self::payload();

      if (isset($payload['user'][$key])) {
        return $payload['user'][$key];
      }
      else {
        return null;
      }
    }

    public static function userId() {
      return self::payloadUser('id');
    }

    public static function userEmail() {
      return self::payloadUser('email');
    }

    public static function signedIn() {
      return !!self::userId();
    }

  }

?>
