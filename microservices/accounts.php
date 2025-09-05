<?php

// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// return all accounts
$connection->query("SELECT * FROM accounts");
error_log(json_encode($connection->errorInfo()));
$accounts = $connection->query("SELECT * FROM accounts")->fetchAll(PDO::FETCH_ASSOC);

return $accounts;
