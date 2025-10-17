<?php
global $connection;

$connection = new PDO('sqlite:db/main.db');

// Review session data
$email = $_SESSION['email'] ?? null;
if (!$email) {
    http_response_code(401);
    return ["error" => "Unauthorized"];
}

return require 'man/' . path()[1] . '.php';
