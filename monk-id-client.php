<?php

// Example message contains:
// $fields = array(
//             'user[email]'=>"etdebruin@gmail.com",
//             'user[password]'=>"monkcheckout",
//             'api_key'=>"68813a03580410a2ca07581a96d36872",
//             'user[authentication_token]'=>$MonkId->user->authentication_token
//           );
//
// @return StdClass Object
// (
//   [success] => 1
//   [user] => stdClass Object
//       (
//           [authentication_token] => 4bzYYXxqvwpvWLYqVvJs
//           [email] => etdebruin@gmail.com
//           [logged_out_at] => 
//           [confirmed_at] => 2012-06-01T22:15:54Z
//       )
// )

class MonkId {

  const post = 'POST';
  const put = 'PUT';
  const delete = 'DELETE';
  const get = 'GET';

  const EMAIL = 'user[email]';
  const PASSWORD = 'user[password]';
  const AUTH_TOKEN = 'user[authentication_token]';

  public static $url = 'https://id.monkdev.com';
  public static $request;

  public static function add($key, $value) {
    if ($key == '') {
      return false;
    }
    self::$request[$key] = $value;
  }

  // The following methods do not require a user authentication token.

  // Opts may contain:
  //   {
  //     email: New email to update the user
  //     password: New password for this user
  //   }
  
  public static function register() {
    return self::api_request(self::post, '/api/users', self::$request);
  }

  public static function login() {
    return self::api_request(self::post, '/api/users/sign_in', self::$request);
  }

  public static function send_password_reset_instructions() {
    return self::api_request(self::post, '/api/users/password', self::$request);
  }

  // The following methods require a user authentication token.

  // Opts may contain:
  //   {
  //     email: New email to update the user
  //     password: New password for this user
  //     authentication_token: REQUIRED - authentication_token for this user
  //   }  

  public static function update() {
    return self::api_request(self::put, '/api/users', self::$request);
  }

  public static function status() {
    return self::api_request(self::post, '/api/users/status', self::$request);
  }

  public static function logout() {
    return self::api_request(self::delete, '/api/users/sign_out', self::$request);
  }

  private function api_request($method, $path, $opts) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::$url . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    switch ($method) {
      case self::post:
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts));
      break;

      case self::put:
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::put);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts));
      break;

      case self::delete:
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::delete);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts));
      break;
    }
    
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    return json_decode($result);
  }

}

?>