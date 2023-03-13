<?php

$dbhost = "127.0.0.1";
$dbuser = "root";
$dbpass = "Root@202202";
$dbname = "projectsampledata";
$dbport = "3306";

$conn = new mysqli($dbhost, $dbuser, $dbpass,$dbname, $dbport);

//check connection
if (mysqli_connect_errno()) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
}


