<?php

$connection = new mysqli("localhost:3307", "root", "", "mydatabase");
// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
} else {
    // echo "Connected successfully";
}