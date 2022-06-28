<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// return all accounts
$accounts = $connection->query("SELECT * FROM account")->fetchAll(PDO::FETCH_ASSOC);

return $accounts;
