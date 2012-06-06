monk-id-client-php
==================

PHP wrapper for Monk ID

Usage
=====

  MonkId::add('user[email]', $_POST['username']);
  MonkId::add('user[password]', $_POST['password']);
  MonkId::add('api_key', 'get_this_from_the_monkid_people');
  $MonkId = MonkId::login();