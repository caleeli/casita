<?php

// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// insert transaction
$id = $path_params[1];

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

// remove transaction
$connection->prepare("DELETE FROM `transactions` WHERE id = :id")->execute([
    'id' => $id,
]);

// Restore account amount
if ($type == 1) {
    // deposito
    $connection->prepare("UPDATE accounts SET balance = balance - :amount WHERE id = :account_id")->execute([
        'amount' => $transaction['amount'],
        'account_id' => $transaction['account_id'],
    ]);
} else {
    // gasto
    $connection->prepare("UPDATE accounts SET balance = balance + :amount WHERE id = :account_id")->execute([
        'amount' => $transaction['amount'],
        'account_id' => $transaction['account_id'],
    ]);
}

return $transaction;
