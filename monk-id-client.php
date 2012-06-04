<?php

class MonkId {

  const POST = 'POST';
  const api_key = '68813a03580410a2ca07581a96d36872';
  const URL = 'https://id.monkdev.com';

  # The following methods do not require a user authentication token.

  # Opts may contain:
  #   {
  #     email: New email to update the user
  #     password: New password for this user
  #   }
  #
  public static function register($opts) {

  }

  public static function login($opts) {
    self::api_request(self::POST, '/api/users/sign_in', $opts);
  }

  private function api_request($method, $path, $opts) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::URL . $path);

    foreach($opts as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string,'&');

    if ($method == self::POST) {
      curl_setopt($ch, CURLOPT_POST,true);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
    }
    
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Display communication with server
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);
  }

}

$fields = array(
            'user[email]'=>urlencode("etdebruin@gmail.com"),
            'user[password]'=>urlencode("monkcheckout"),
            'api_key'=>urlencode("68813a03580410a2ca07581a96d36872")
        );

MonkId::login($fields);

?>