<?php

global $connection;

// Validate input
if (empty($_POST['name']) || empty($_POST['email'])) {
    http_response_code(400);
    return ["error" => "Faltan datos requeridos"];
}

$connection->exec("insert into wallet_by_email (name, email) values (?, ?)", [
    $_POST['name'],
    $_POST['email'],
]);
