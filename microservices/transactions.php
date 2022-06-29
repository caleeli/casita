<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

$page_size = $_GET['page_size'] ?? 10;
$page = $_GET['page'] ?? 1;

// select
$sql = "SELECT `transactions`.*, accounts.name FROM `transactions` left join accounts on (transactions.account_id = accounts.id) ORDER BY `transactions`.created_at DESC LIMIT :page_size OFFSET :offset";
$stmt = $connection->prepare($sql);
$stmt->execute([
    'page_size' => $page_size,
    'offset' => ($page - 1) * $page_size,
]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $transactions;
