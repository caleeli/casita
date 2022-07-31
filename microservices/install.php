<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

return "disabled";

// drop table if exists
$connection->exec("DROP TABLE IF EXISTS `accounts`");
$connection->exec("DROP TABLE IF EXISTS `transactions`");
// create table: account
$connection->exec("CREATE TABLE IF NOT EXISTS accounts (
    id INTEGER PRIMARY KEY,
    name TEXT,
    balance INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// table: transaction
$connection->exec("CREATE TABLE IF NOT EXISTS `transactions` (
    id INTEGER PRIMARY KEY,
    account_id INTEGER,
    amount INTEGER,
    type INTEGER,
    memo TEXT,
    created_at DATE
)");

// create accounts
// General
// $connection->exec("INSERT INTO accounts (name, balance) VALUES ('General', 0)");
// David
$connection->exec("INSERT INTO accounts (name, balance) VALUES ('David', 0)");
// Patricia
$connection->exec("INSERT INTO accounts (name, balance) VALUES ('Patricia', 0)");
// Mama
$connection->exec("INSERT INTO accounts (name, balance) VALUES ('Mama', 0)");
