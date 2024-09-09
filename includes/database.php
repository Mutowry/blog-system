<?php

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "blogsystem";

//connection to database
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Databse connection failed!");
}
