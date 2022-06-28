<?php

// sqlite connection
$connection = new PDO('sqlite:db/estado.db');

// create tables if not exists
// table: account
$connection->exec("CREATE TABLE IF NOT EXISTS account (
    id INTEGER PRIMARY KEY,
    name TEXT,
    balance INTEGER,
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

// create accounts
// General
$connection->exec("INSERT INTO account (name, balance) VALUES ('General', 0)");
// David
$connection->exec("INSERT INTO account (name, balance) VALUES ('David', 0)");
// Patricia
$connection->exec("INSERT INTO account (name, balance) VALUES ('Patricia', 0)");
// Mama
$connection->exec("INSERT INTO account (name, balance) VALUES ('Mama', 0)");
