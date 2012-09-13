<?php

  /**
   * PHP client for Monk ID.
   *
   * @author    Justin Stayton <jstayton@monkdevelopment.com>
   * @copyright Copyright 2012 by Monk Development, Inc.
   * @license   http://en.wikipedia.org/wiki/MIT_License MIT License
   * @package   MonkId
   * @version   0.2.0
   */
  class MonkId {

    /**
     * Authentication token field of `user` object in API requests/responses.
     */
    const AUTH_TOKEN = 'authentication_token';

    /**
     * Confirmed date/time field of `user` object in API requests/responses.
     */
    const CONFIRMED_AT = 'confirmed_at';

    /**
     * Email field of `user` object in API requests/responses.
     */
    const EMAIL = 'email';

    /**
     * Last log in date/time field of `user` object in API requests/responses.
     */
    const LAST_LOG_IN_AT = 'last_sign_in_at';

    /**
     * Logged in date/time field of `user` object in API requests/responses.
     */
    const LOGGED_IN = 'signed_in';

    /**
     * Logged out date/time field of `user` object in API requests/responses.
     */
    const LOGGED_OUT_AT = 'logged_out_at';

    /**
     * One time token field of `user` object in API requests/responses.
     */
    const ONE_TIME_TOKEN = 'one_time_token';

    /**
     * Password field of `user` object in API requests/responses.
     */
    const PASSWORD = 'password';

    /**
     * An unknown error occurred.
     */
    const STATUS_UNKNOWN_ERROR = -1;

    /**
     * Request successful.
     */
    const STATUS_SUCCESS = 0;

    /**
     * Invalid email address.
     */
    const STATUS_INVALID_EMAIL = 1;

    /**
     * Email address already exists.
     */
    const STATUS_EMAIL_EXISTS = 2;

    /**
     * Invalid password.
     */
    const STATUS_INVALID_PASSWORD = 3;

    /**
     * Invalid password, cannot be less than six characters.
     */
    const STATUS_INVALID_PASSWORD_SHORT = 4;

    /**
     * Invalid password, cannot be blank.
     */
    const STATUS_INVALID_PASSWORD_BLANK = 5;

    /**
     * DELETE method for HTTP requests.
     */
    const HTTP_DELETE = 'DELETE';

    /**
     * GET method for HTTP requests.
     */
    const HTTP_GET = 'GET';

    /**
     * POST method for HTTP requests.
     */
    const HTTP_POST = 'POST';

    /**
     * PUT method for HTTP requests.
     */
    const HTTP_PUT = 'PUT';

    /**
     * Config to use across all requests.
     *
     * @var array
     */
    private static $config = array();

    /**
     * Load an ini config from a file to use across all requests.
     *
     * @link   http://php.net/manual/en/function.parse-ini-file.php
     * @link   http://en.wikipedia.org/wiki/INI_file
     * @param  string $path path to the config file to load
     * @param  string $environment environment name within the config to load
     * @return bool true if loaded, false if failed to load or no config
     */
    public static function loadConfig($path, $environment) {
      $config = parse_ini_file($path, true);

      if (!$config || !array_key_exists($environment, $config)) {
        return false;
      }

      self::$config = $config[$environment];

      return true;
    }

    /**
     * Set a config value to use across all requests.
     *
     * @param  string $key key to set
     * @param  mixed $value value to set
     * @return MonkId this class
     */
    public static function setConfig($key, $value) {
      self::$config[$key] = $value;

      return self;
    }

    /**
     * Get a config value that's used across all requests.
     * 
     * @param  string $key key value to get
     * @return mixed|null key value or null if not set
     */
    public static function getConfig($key) {
      return array_key_exists($key, self::$config) ? self::$config[$key] : null;
    }

    /**
     * Register a new user.
     * 
     * @param  string $email email address of user
     * @param  string $password password of user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function register($email, $password) {
      $params = array(self::EMAIL    => $email,
                      self::PASSWORD => $password);

      return self::request(self::HTTP_POST, '/', $params);
    }

    /**
     * Update a user's username and/or password.
     * 
     * @param  string $email new email address of user
     * @param  string $password new password of user
     * @param  string $authToken authentication token for user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function update($email, $password, $authToken) {
      $params = array(self::AUTH_TOKEN => $authToken,
                      self::EMAIL      => $email,
                      self::PASSWORD   => $password);

      return self::request(self::HTTP_PUT, '/', $params);
    }

    /**
     * Send a password reset email to a user.
     * 
     * @param  string $email email address of user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function passwordReset($email) {
      $params = array(self::EMAIL => $email);

      return self::request(self::HTTP_POST, '/password', $params);
    }

    /**
     * Log a user in to Monk ID.
     * 
     * @param  string $email email address of user
     * @param  string $password password of user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function logIn($email, $password) {
      $params = array(self::EMAIL    => $email,
                      self::PASSWORD => $password);

      return self::request(self::HTTP_POST, '/sign_in', $params);
    }

    /**
     * Log a user out of Monk ID.
     * 
     * @param  string $authToken authentication token for user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function logOut($authToken) {
      $params = array(self::AUTH_TOKEN => $authToken);

      return self::request(self::HTTP_DELETE, '/sign_out', $params);
    }

    /**
     * Get the log in status of a user.
     * 
     * @param  string $authToken authentication token for user
     * @return array|false JSON-decoded response or false if request failed
     */
    public static function status($authToken) {
      $params = array(self::AUTH_TOKEN => $authToken);

      return self::request(self::HTTP_POST, '/status', $params);
    }

    /**
     * Build the full request URL, including protocol, host, port, and path.
     * 
     * @param  string $endpoint endpoint of the URL
     * @return string|false full request URL or false if host is not configured
     */
    private static function url($endpoint) {
      $host = self::getConfig('host');

      if (!$host) {
        return false;
      }

      $protocol = self::getConfig('ssl') ? 'https' : 'http';
      $port = self::getConfig('port');
      $path = '/api/users';

      return $protocol . '://' . $host . ($port ? ':' . $port : '') . $path . $endpoint;
    }

    /**
     * Build the parameters to send with the request.
     * 
     * @param  array $user `user` key values
     * @param  bool $queryString optional whether to encode to query string
     * @return array|string string if encoded to query string
     */
    private static function params(array $user, $queryString = true) {
      $params = array('api_key' => self::getConfig('api_key'),
                      'user'    => $user);

      return $queryString ? http_build_query($params) : $params;
    }

    /**
     * Make an HTTP API request using cURL.
     * 
     * @param  string $method HTTP method (GET, POST, etc.)
     * @param  string $endpoint endpoint of the URL
     * @param  array $params query string params to encode with the request
     * @return array|false JSON-decoded response or false if request failed
     */
    private static function request($method, $endpoint, array $params) {
      $url = self::url($endpoint);

      if (!$url) {
        return false;
      }

      $request = curl_init();

      curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($request, CURLOPT_POSTFIELDS, self::params($params));
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($request, CURLOPT_URL, $url);

      $response = curl_exec($request);

      curl_close($request);

      return $response ? json_decode($response, true) : $response;
    }

  }

?>
