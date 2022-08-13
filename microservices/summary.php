<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// return all accounts
$summary = $connection
    ->query("SELECT 'HOY' as name, SUM(amount*`type`) as balance
        FROM transactions
        WHERE created_at = date('now')")
    ->fetchAll(PDO::FETCH_ASSOC);

return $summary;
