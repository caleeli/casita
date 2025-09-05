<?php

// sqlite connection
//$connection = new PDO('sqlite:db/estado.db');

// create tables if not exists
// table: account
$connection->exec("CREATE TABLE IF NOT EXISTS account (
    id INTEGER PRIMARY KEY,
    name TEXT,
    amount INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// table: transaction
$connection->exec("CREATE TABLE IF NOT EXISTS transaction (
    id INTEGER PRIMARY KEY,
    account_id INTEGER,
    amount INTEGER,
    memo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
