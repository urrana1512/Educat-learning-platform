<?php

// Change detials in both variables $conn and $con becouse both variables are used.

$hostname = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

$conn = new mysqli("localhost","root","","educat");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>