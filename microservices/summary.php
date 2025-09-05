<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// return all accounts
$summary = $connection
    ->query("SELECT 'HOY' as name, SUM(amount*`type`) as balance
        FROM transactions
        WHERE created_at = date('now')")
    ->fetchAll(PDO::FETCH_ASSOC);
$query2 = $connection
    ->query("SELECT 'OBRA' as name, SUM(amount) as balance
        FROM transactions
        WHERE memo like '%obra%' and type=-1");
if (!$query2) {
    throw new Exception($connection->errorInfo()[2]);
}
$summary2 = $query2->fetchAll(PDO::FETCH_ASSOC);

return array_merge($summary, $summary2);
