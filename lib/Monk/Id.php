<?php

  /**
   * Global Monk namespace.
   */
  namespace Monk;

  /**
   * Integrate Monk ID on the server-side by accessing payloads from the
   * client-side JavaScript.
   *
   * @author    Monk Development, Inc.
   * @copyright 2013 Monk Development, Inc.
   */
  class Id {

    /**
     * Name of the cookie that (optionally) stores the payload.
     */
    const COOKIE_NAME = '_monkIdPayload';

    /**
     * Config values.
     *
     * @var array
     */
    private static $config;

    /**
     * Decoded and verified payload.
     *
     * @var array
     */
    private static $payload;

    /**
     * Prevent the class from being instantiated as all data and methods are
     * static.
     */
    private function __construct() { }

    /**
     * Load an INI config file for a specific environment.
     *
     * @param  string $path Path of INI config file to load.
     * @param  string $environment Environment section to use. Defaults to
     *         `development`.
     * @return array Loaded config values.
     */
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

    /**
     * Set a config value.
     *
     * @param  string $key Name of config value.
     * @param  mixed $value New config value.
     * @return self
     */
    public static function setConfig($key, $value) {
      self::$config[$key] = $value;

      return self;
    }

    /**
     * Get a config value.
     *
     * @param  string $key Name of config value.
     * @return mixed Config value or `null` if not set.
     */
    public static function getConfig($key) {
      return isset(self::$config[$key]) ? self::$config[$key] : null;
    }

    /**
     * Get the encoded payload stored in the cookie.
     *
     * @return string|null Encoded payload or `null` if not set.
     */
    private static function cookiePayload() {
      return isset($_COOKIE[self::COOKIE_NAME]) ? $_COOKIE[self::COOKIE_NAME] : null;
    }

    /**
     * Decode a payload from the client-side.
     *
     * @param  string $encodedPayload Encoded payload.
     * @return array Decoded payload.
     * @throws Exception If payload cannot be decoded.
     */
    private static function decodePayload($encodedPayload) {
      $decodedPayload = json_decode(base64_decode($encodedPayload), true);

      if (!$decodedPayload) {
        throw new Exception('failed to decode payload');
      }

      return $decodedPayload;
    }

    /**
     * Generate the expected signature of a payload using the app's secret.
     *
     * @param  array $payload Decoded payload.
     * @return string Expected signature of the payload.
     */
    private static function expectedSignature(array $payload) {
      unset($payload['user']['signature']);

      return hash_hmac('sha512', json_encode($payload['user']), self::getConfig('app_secret'), true);
    }

    /**
     * Verify that a payload hasn't been tampered with or faked by comparing
     * signatures.
     *
     * @param  array $payload Decoded payload.
     * @return bool Whether the payload is legit.
     */
    private static function verifyPayload(array $payload) {
      $signature = base64_decode($payload['user']['signature']);

      return $signature == self::expectedSignature($payload);
    }

    /**
     * Load a payload from the client-side.
     *
     * @param  string|array $encodedPayload Encoded payload or cookies array to
     *         automatically load the payload from.
     * @return array Decoded and verified payload. Empty if there's no payload
     *         or it fails verification.
     */
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

      try {
        $payload = self::decodePayload($payload);
        $verified = self::verifyPayload($payload);
      }
      catch (Exception $e) {
        $verified = false;
      }

      return self::$payload = $verified ? $payload : array();
    }

    /**
     * Get the loaded payload.
     *
     * @return array Loaded payload. Empty if there's no payload or it failed
     *         verification.
     */
    private static function payload() {
      if (!isset(self::$payload)) {
        self::loadPayload();
      }

      return self::$payload;
    }

    /**
     * Get a value from the `user` object of the loaded payload.
     *
     * @param  string $key Name of value.
     * @return mixed Requested value or `null` if not set.
     */
    private static function payloadUser($key) {
      $payload = self::payload();

      if (isset($payload['user'][$key])) {
        return $payload['user'][$key];
      }
      else {
        return null;
      }
    }

    /**
     * Get the signed in user's UUID.
     *
     * @return string|null UUID if signed in user or `null` if no signed in
     *         user.
     */
    public static function userId() {
      return self::payloadUser('id');
    }

    /**
     * Get the signed in user's email address.
     *
     * @return string|null Email address if signed in user or `null` if no
     *         signed in user.
     */
    public static function userEmail() {
      return self::payloadUser('email');
    }

    /**
     * Check whether there's a signed in user.
     *
     * @return bool Whether there's a signed in user.
     */
    public static function signedIn() {
      return !!self::userId();
    }

  }

?>
