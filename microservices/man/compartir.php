<?php

global $connection;

// Validate input
if (empty($_POST['name']) || empty($_POST['email'])) {
    http_response_code(400);
    return ["error" => "Faltan datos requeridos"];
}

$statement = $connection->prepare("insert into wallet_by_email (name, email) values (?, ?)");
$statement->execute([
    $_POST['name'],
    $_POST['email']
]);
if ($statement->rowCount() === 0) {
    http_response_code(500);
    return ["error" => "No se pudo compartir el fondo"];
}

return ["success" => true];
