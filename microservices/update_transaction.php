<?php

// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// update transaction
$id = $path_params[1];
$account_id = $_POST['account_id'];
$amount = abs($_POST['amount']);
$type = $_POST['type'];
$memo = $_POST['memo'];
$created_at = $_POST['created_at'];

// select transaction
$sql = "SELECT * FROM `transactions` WHERE id = :id";
$stmt = $connection->prepare($sql);
$stmt->execute([
    'id' => $id,
]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    return "Transaction not found";
}

$connection->prepare("UPDATE `transactions` SET account_id=:account_id, amount=:amount, type=:type, memo=:memo, created_at=:created_at WHERE id=:id")->execute([
    ':id' => $id,
    ':account_id' => $account_id,
    ':amount' => $amount,
    ':type' => $type,
    ':memo' => $memo,
    ':created_at' => $created_at,
]);

// Discount previous account amount
if ($transaction['type'] == 1) {
    // deposito
    $connection->prepare("UPDATE accounts SET balance = balance - :amount WHERE id = :id")->execute([
        ':id' => $transaction['account_id'],
        ':amount' => $transaction['amount'],
    ]);
} else {
    // gasto
    $connection->prepare("UPDATE accounts SET balance = balance + :amount WHERE id = :id")->execute([
        ':id' => $transaction['account_id'],
        ':amount' => $transaction['amount'],
    ]);
}
// Update account amount
if ($type == 1) {
    // deposito
    $connection->prepare("UPDATE accounts SET balance = balance + :amount WHERE id = :id")->execute([
        ':id' => $account_id,
        ':amount' => $amount,
    ]);
} else {
    // gasto
    $connection->prepare("UPDATE accounts SET balance = balance - :amount WHERE id = :id")->execute([
        ':id' => $account_id,
        ':amount' => $amount,
    ]);
}
