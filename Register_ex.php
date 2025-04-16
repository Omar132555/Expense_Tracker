<?php
require_once 'Connection.php';
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$username = filter_input(INPUT_POST, 'username', FILTER_DEFAULT);
$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
if(!$username || !$password) {
    echo "Username and password Format are inValid!";
    $error = true;
    exit;
}
else
{
    $error = false;
}
if(!$error)
{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);
if($stmt->execute()){
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
}
else{
    echo "Error: ";
}
?>