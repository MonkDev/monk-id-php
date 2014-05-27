<?php

  define('DS', DIRECTORY_SEPARATOR);
  define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
  define('LIB_PATH', ROOT_PATH . DS . 'lib');
  define('TESTS_PATH', ROOT_PATH . DS . 'tests');
  define('TESTS_CONFIG_PATH', TESTS_PATH . DS . 'config');

  require TESTS_PATH . DS . 'Helpers.php';
  require LIB_PATH . DS . 'Monk' . DS . 'Id.php';

?>
