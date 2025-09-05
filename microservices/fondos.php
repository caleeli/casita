<?php

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
