<?php

// Review session data
$email = $_SESSION['email'] ?? null;
if (!$email) {
    http_response_code(401);
    return ["error" => "Unauthorized", "dbg" => $_SESSION];
}

// sqlite connection
$connection = new PDO('sqlite:db/main.db');

$connection->exec("CREATE TABLE IF NOT EXISTS wallet_by_email (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    archived INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$connection->exec("CREATE INDEX wallet_by_email_idx ON wallet_by_email (email);");

$wallet = $connection->query("SELECT `name` FROM wallet_by_email WHERE email = '$email'")->fetchAll(PDO::FETCH_ASSOC);

return $wallet;

///////////////////////////////////////////////////////
// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// Obtener user_id desde GET o POST
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    throw new InvalidArgumentException('user_id requerido', 422);
}

// Consulta para obtener los fondos del usuario
$stmt = $connection->prepare('SELECT id, nombre FROM fondos
    INNER JOIN usuario_fondo on fondos.id = usuario_fondo.fondo_id
    WHERE usuario_fondo.usuario_id = ?');
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

return ['fondos' => $fondos];
