<?php

session_start();

// db credentials are kept here.
require_once('../wfo_secret.php');

define(WFO_DEFAULT_YEAR, 2019);

define(WFO_TABLE_NAME, 'wfo_' . WFO_DEFAULT_YEAR . '_classification');

// create and initialise the database connection
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_database);

// connect to the database
if ($mysqli->connect_error) {
  echo $mysqli->connect_error;
}

if (!$mysqli->set_charset("utf8")) {
  echo printf("Error loading character set utf8: %s\n", $mysqli->error);
}



?>