<?php
/**
 * Global Monk namespace.
 */
namespace Monk;

/**
 * Integrate Monk ID authentication and single sign-on for apps and websites
 * on the server-side.
 *
 * @author    Monk Development, Inc.
 * @copyright 2014 Monk Development, Inc.
 */
class Id
{
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
    private function __construct()
    {
    }

    /**
     * Load an INI config file for a specific environment.
     *
     * @param  string $path Path of INI config file to load. Leave `null` to
     *   read from environment's `MONK_ID_CONFIG` value.
     * @param  string $environment Environment section to use. Leave `null` to
     *   read from environment's `MONK_ID_ENV` value. Defaults to `development`.
     * @return array Loaded config values.
     * @throws \Exception If the file doesn't exist or can't be read.
     */
    public static function loadConfig($path = null, $environment = null)
    {
        $path = $path ? $path : getenv('MONK_ID_CONFIG');
        $environment = $environment ? $environment : getenv('MONK_ID_ENV');
        $environment = $environment ? $environment : 'development';

        $config = parse_ini_file($path, true);
        $config = $config[$environment];

        self::verifyConfig($config);

        return self::$config = $config;
    }

    /**
     * Verify that a config has all the required values.
     *
     * @param  array $config Config values.
     * @return true If valid.
     * @throws \Exception If invalid.
     */
    private static function verifyConfig(array $config = null)
    {
        if (!$config) {
            throw new \Exception('no config loaded');
        } elseif (!$config['app_id']) {
            throw new \Exception('no `app_id` config value');
        } elseif (!$config['app_secret']) {
            throw new \Exception('no `app_secret` config value');
        }

        return true;
    }

    /**
     * Get or set a config value. Attempts to load the config if it hasn't
     * already been loaded.
     *
     * @param  string $key Name of config value.
     * @param  mixed $value New config value. Leave unset to get a config value.
     * @return mixed Config value.
     * @throws \Exception If the config can't be loaded or is invalid.
     */
    public static function config($key, $value = null)
    {
        // If both parameters are passed, set the value. Otherwise, if the
        // config hasn't been loaded, attempt to load to get a value.
        if (func_num_args() == 2) {
            $config = isset(self::$config) ? self::$config : array();
            $config[$key] = $value;

            self::verifyConfig($config);

            self::$config = $config;
        } elseif (!isset(self::$config)) {
            self::loadConfig();
        }

        return self::$config[$key];
    }

    /**
     * Select a payload from the first place one can be found. First in the
     * `$encodedPayload` param, then in the global `$_COOKIE`.
     *
     * @param  string|array $encodedPayload Encoded payload or cookies array to
     *   select the payload from.
     * @return string Encoded payload.
     */
    private static function selectPayload($encodedPayload = null)
    {
        if ($encodedPayload) {
            if (is_array($encodedPayload)) {
                $payload = $encodedPayload[self::COOKIE_NAME];
            } else {
                $payload = $encodedPayload;
            }
        } else {
            $payload = $_COOKIE[self::COOKIE_NAME];
        }

        return $payload;
    }

    /**
     * Decode a payload from the client-side.
     *
     * @param  string $encodedPayload Encoded payload.
     * @return array Decoded payload.
     * @throws \Exception If payload can't be decoded.
     */
    private static function decodePayload($encodedPayload)
    {
        $decodedPayload = json_decode(base64_decode($encodedPayload), true);

        if (!$decodedPayload) {
            throw new \Exception('failed to decode payload');
        }

        return $decodedPayload;
    }

    /**
     * Generate the expected signature of a payload using the app's secret.
     *
     * @param  array $payload Decoded payload.
     * @return string Expected signature of the payload.
     */
    private static function expectedSignature(array $payload)
    {
        unset($payload['user']['signature']);

        return hash_hmac('sha512', json_encode($payload['user']), self::config('app_secret'), true);
    }

    /**
     * Verify that a payload hasn't been tampered with or faked by comparing
     * signatures.
     *
     * @param  array $payload Decoded payload.
     * @return bool Whether the payload is legit.
     */
    private static function verifyPayload(array $payload)
    {
        $signature = base64_decode($payload['user']['signature']);

        return $signature == self::expectedSignature($payload);
    }

    /**
     * Clean a payload of values that shouldn't be made accessible to the app.
     *
     * @param  array $payload Decoded payload.
     * @return array Cleaned payload.
     */
    private static function cleanPayload(array $payload)
    {
        unset($payload['user']['signature']);

        return $payload;
    }

    /**
     * Load a payload from the client-side.
     *
     * @param  string|array $encodedPayload Encoded payload or cookies array to
     *   automatically load the payload from. Leave `null` to read from global
     *   `$_COOKIE`.
     * @return array Decoded and verified payload. Empty if there's no payload
     *   or it fails verification.
     */
    public static function loadPayload($encodedPayload = null)
    {
        $payload = self::selectPayload($encodedPayload);

        if (!$payload) {
            return self::$payload = array();
        }

        try {
            $payload = self::decodePayload($payload);
            $verified = self::verifyPayload($payload);
            $payload = self::cleanPayload($payload);
        } catch (\Exception $e) {
            $verified = false;
        }

        return self::$payload = $verified ? $payload : array();
    }

    /**
     * Get the loaded payload.
     *
     * @return array Loaded payload. Empty if there's no payload or it failed
     *   verification.
     */
    private static function payload()
    {
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
    private static function payloadUser($key)
    {
        $payload = self::payload();

        if (isset($payload['user'][$key])) {
            return $payload['user'][$key];
        } else {
            return null;
        }
    }

    /**
     * Get the signed in user's UUID.
     *
     * @return string|null UUID if signed in user or `null` if no signed in
     *   user.
     */
    public static function userId()
    {
        return self::payloadUser('id');
    }

    /**
     * Get the signed in user's email address.
     *
     * @return string|null Email address if signed in user or `null` if no
     *   signed in user.
     */
    public static function userEmail()
    {
        return self::payloadUser('email');
    }

    /**
     * Check whether there's a signed in user.
     *
     * @return bool Whether there's a signed in user.
     */
    public static function signedIn()
    {
        return !!self::userId();
    }
}
