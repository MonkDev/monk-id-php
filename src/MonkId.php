<?php

  namespace Monk;

  class Id {

    private static $instance;

    private $config = array();
    private $payload;

    private function __construct() { }

    final private function __clone() { }

    public static function getInstance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
      }

      return self::$instance;
    }

    public function loadConfig($path, $environment) {
      $config = parse_ini_file($path, true);

      if (!$config || !isset($config[$environment])) {
        return false;
      }

      $this->config = $config[$environment];

      return true;
    }

    public function setConfig($key, $value) {
      $this->config[$key] = $value;

      return $this;
    }

    public function getConfig($key) {
      return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    private function cookiePayload() {
      return isset($_COOKIE['_monkIdPayload']) ? $_COOKIE['_monkIdPayload'] : null;
    }

    private function decodePayload($payload) {
      return json_decode(base64_decode($payload), true);
    }

    private function verifyPayload($payload) {
      if (!$payload) {
        return false;
      }

      $signature = base64_decode($payload['user']['signature']);

      unset($payload['user']['signature']);

      $expectedSignature = hash_hmac('sha512', json_encode($payload['user']), $this->getConfig('appSecret'), true);

      return $signature == $expectedSignature;
    }

    public function loadPayload($encodedPayload = null) {
      $payload = $encodedPayload ? $encodedPayload : $this->cookiePayload();

      if (!$payload) {
        $this->payload = null;

        return false;
      }

      $payload = $this->decodePayload($payload);
      $verified = $this->verifyPayload($payload);

      $this->payload = $verified ? $payload : null;

      return $verified;
    }

    private function payload() {
      if (!isset($this->payload)) {
        $this->loadPayload();
      }

      return $this->payload;
    }

    private function payloadValue($key) {
      $payload = $this->payload();

      if (isset($payload['user'][$key])) {
        return $payload['user'][$key];
      }
      else {
        return null;
      }
    }

    public function userId() {
      return $this->payloadValue('id');
    }

    public function userEmail() {
      return $this->payloadValue('email');
    }

  }

?>
