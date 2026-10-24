<?php

global $connection;

$path = path();
$sourceFund = $path[1] ?? '';
$transactionId = filter_var($path[3] ?? null, FILTER_VALIDATE_INT);
$targetFund = trim((string) ($_POST['target_fondo'] ?? ''));
$targetAccountId = filter_var($_POST['account_id'] ?? null, FILTER_VALIDATE_INT);

if (!$transactionId || !$targetAccountId || $targetFund === '') {
    http_response_code(400);
    return ['error' => 'Faltan datos para mover el registro'];
}
if (
    $targetFund === $sourceFund
    || strpos($targetFund, '/') !== false
    || strpos($targetFund, '\\') !== false
    || $targetFund === '.'
    || $targetFund === '..'
) {
    http_response_code(400);
    return ['error' => 'El fondo de destino no es válido'];
}

$targetDb = 'db/' . $targetFund . '.db';
if (!is_file($targetDb)) {
    http_response_code(404);
    return ['error' => 'No se encontró la base de datos del fondo de destino'];
}

$attached = false;
try {
    $connection->exec('ATTACH DATABASE ' . $connection->quote($targetDb) . ' AS target_fund');
    $attached = true;
    $connection->beginTransaction();

    $transactionQuery = $connection->prepare('SELECT * FROM transactions WHERE id = :id');
    $transactionQuery->execute([':id' => $transactionId]);
    $transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC);
    if (!$transaction) {
        throw new RuntimeException('No se encontró el registro de origen', 404);
    }

    $targetAccount = $connection->prepare('SELECT id FROM target_fund.accounts WHERE id = :id');
    $targetAccount->execute([':id' => $targetAccountId]);
    if (!$targetAccount->fetchColumn()) {
        throw new RuntimeException('No se encontró la cuenta de destino', 404);
    }

    $insert = $connection->prepare(
        'INSERT INTO target_fund.transactions (account_id, amount, type, memo, created_at)
         VALUES (:account_id, :amount, :type, :memo, :created_at)'
    );
    $insert->execute([
        ':account_id' => $targetAccountId,
        ':amount' => $transaction['amount'],
        ':type' => $transaction['type'],
        ':memo' => $transaction['memo'],
        ':created_at' => $transaction['created_at'],
    ]);
    $newTransactionId = $connection->lastInsertId();

    if ((int) $transaction['type'] === 1) {
        $balanceChange = $connection->prepare('UPDATE target_fund.accounts SET balance = balance + :amount WHERE id = :id');
    } else {
        $balanceChange = $connection->prepare('UPDATE target_fund.accounts SET balance = balance - :amount WHERE id = :id');
    }
    $balanceChange->execute([':amount' => $transaction['amount'], ':id' => $targetAccountId]);

    if ((int) $transaction['type'] === 1) {
        $balanceChange = $connection->prepare('UPDATE accounts SET balance = balance - :amount WHERE id = :id');
    } else {
        $balanceChange = $connection->prepare('UPDATE accounts SET balance = balance + :amount WHERE id = :id');
    }
    $balanceChange->execute([':amount' => $transaction['amount'], ':id' => $transaction['account_id']]);

    $delete = $connection->prepare('DELETE FROM transactions WHERE id = :id');
    $delete->execute([':id' => $transactionId]);
    if ($delete->rowCount() !== 1) {
        throw new RuntimeException('No se pudo quitar el registro del fondo de origen', 409);
    }

    $connection->commit();
} catch (Throwable $e) {
    if ($connection->inTransaction()) {
        $connection->rollBack();
    }
    throw $e;
} finally {
    if ($attached) {
        $connection->exec('DETACH DATABASE target_fund');
    }
}

return [
    'success' => true,
    'source_id' => $transactionId,
    'destination_id' => $newTransactionId,
    'destination_fondo' => $targetFund,
];
