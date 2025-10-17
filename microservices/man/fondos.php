<?php

global $connection;

$connection->exec("CREATE TABLE IF NOT EXISTS wallet_by_email (
    id INTEGER PRIMARY KEY,
    name TEXT,ad
    email TEXT,
    archived INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Create index if not exists
try {
    $connection->exec("CREATE INDEX wallet_by_email_idx ON wallet_by_email (email);");
} catch (Exception $e) {
    // Index might already exist
}
// Create constraint unique(name, email)
try {
    $connection->exec("CREATE UNIQUE INDEX wallet_by_email_unique ON wallet_by_email (name, email);");
} catch (Exception $e) {
    // Constraint might already exist
}

$wallet = $connection->query("SELECT `name` FROM wallet_by_email WHERE email = '$email'")->fetchAll(PDO::FETCH_ASSOC);

return $wallet;
