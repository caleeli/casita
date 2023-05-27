<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

$page_size = $_GET['page_size'] ?? 10;
$page = $_GET['page'] ?? 1;
$params = [
    'page_size' => $page_size,
    'offset' => ($page - 1) * $page_size,
];

// filters
$account_id = $_GET['account_id'] ?? null;
$amount = $_GET['amount'] ?? null;
$created_at = $_GET['created_at'] ?? null;
$memo = $_GET['memo'] ?? null;
$where = [
    '1 = 1',
];
if ($account_id) {
    $where[] = "account_id = :account_id";
    $params['account_id'] = $account_id;
}
if ($amount) {
    $where[] = "amount = :amount";
    $params['amount'] = $amount;
}
if ($created_at) {
    $where[] = "transactions.created_at = :created_at";
    $params['created_at'] = $created_at;
}
if ($memo) {
    $where[] = "memo like :memo";
    $params['memo'] = "%$memo%";
}
$where = implode(' AND ', $where);

// select
$sql = "SELECT `transactions`.*, accounts.name FROM `transactions` left join accounts on (transactions.account_id = accounts.id) WHERE {$where} ORDER BY `transactions`.created_at DESC, `transactions`.id DESC LIMIT :page_size OFFSET :offset";
$stmt = $connection->prepare($sql);
if (!$stmt) {
    throw new Exception($connection->errorInfo()[2]);
}
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $transactions;
