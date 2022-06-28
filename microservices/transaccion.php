<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// insert transaction
$account_id = $_POST['account_id'];
$amount = $_POST['amount'];
$memo = $_POST['memo'];
$connection->prepare("INSERT INTO transaction (account_id, amount, memo) VALUES (:account_id, :amount, :memo)")->execute([
    ':account_id' => $account_id,
    ':amount' => $amount,
    ':memo' => $memo
]);

// Update account amount
$connection->prepare("UPDATE account SET balance = balance + :amount WHERE id = :account_id")->execute([
    ':account_id' => $account_id,
    ':amount' => $amount
]);
