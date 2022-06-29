<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// return all accounts
$accounts = $connection->query("SELECT * FROM accounts")->fetchAll(PDO::FETCH_ASSOC);

return $accounts;
