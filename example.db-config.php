<?php 




// Define db variables.
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_BASE", "sheetmusic");

// Global MySQL Database Connection.
global $mysqli;
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_BASE);
if ($mysqli->connect_error || $mysqli->connect_errno) {
  exit("<h1 class='text-danger'>Database Connection Error (" . $mysqli->connect_errno . "): " . $mysqli->connect_error . "</h1>");
}

$mysqli->set_charset("utf8");