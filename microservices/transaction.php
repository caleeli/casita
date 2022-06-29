<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// insert transaction
$account_id = $_POST['account_id'];
$amount = abs($_POST['amount']);
$type = $_POST['type'];
$memo = $_POST['memo'];
$created_at = $_POST['created_at'];
$connection->prepare("INSERT INTO `transactions` (account_id, amount, type, memo, created_at) VALUES (:account_id, :amount, :type, :memo, :created_at)")->execute([
    ':account_id' => $account_id,
    ':amount' => $amount,
    ':type' => $type,
    ':memo' => $memo,
    ':created_at' => $created_at,
]);

// Update account amount
if ($type == 1) {
    // deposito
    $connection->exec("UPDATE accounts SET balance = balance + $amount WHERE id = $account_id");
} else {
    // gasto
    $connection->exec("UPDATE accounts SET balance = balance - $amount WHERE id = $account_id");
}
