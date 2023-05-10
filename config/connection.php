<?php

$DATABASE_HOST = "localhost";
$DATABASE_PORT = 3306;
$DATABASE_USER = "root";
$DATABASE_PSWD = "";
$DATABASE_NAME = "todo";

$connection = mysqli_connect(
  $DATABASE_HOST,
  $DATABASE_USER,
  $DATABASE_PSWD,
  $DATABASE_NAME,
  $DATABASE_PORT
);

if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}
