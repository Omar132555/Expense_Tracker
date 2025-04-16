<?php

require_once 'Connection.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$username = filter_input(INPUT_POST, 'username', FILTER_DEFAULT);
$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
if(!$username || !$password) {
    echo "Username and password are required!";
    $error = true;
    exit;
}
$stmt = $connection->prepare("SELECT * from users where username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user && password_verify($password, $user['password'])) {
    echo "Login successful! Welcome " . $user['username'];

    $payload = [
        "iss" => "http://localhost/your_project",
        "aud" => "http://localhost/your_project",
        "iat" => time(),
        "exp" => time() + (60 * 60),
        "data" => [
            "username" => $user['username'],
            'ID' => $user['ID']
        ]
    ];
    $key = 'bgCptJDTIGLaGA5fdSWnsXcVx9e4AZezqhJ7GpGNcjU=';
    // Generate JWT token (Authorization Part)
    $jwt = JWT::encode($payload, $key, 'HS256');
    setcookie('jwt-token', $jwt, time() + (60 * 60), "/");
    http_response_code(200);
} else {
    echo "Invalid username or password";
    http_response_code(401);
    exit;
}
$stmt->close();

?>