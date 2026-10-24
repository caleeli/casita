<?php

$sql = "SELECT
    strftime('%Y', created_at) AS year,
    SUM(CASE WHEN lower(COALESCE(memo, '')) LIKE '%obra%' THEN amount ELSE 0 END) AS construction,
    SUM(CASE WHEN lower(COALESCE(memo, '')) NOT LIKE '%obra%' THEN amount ELSE 0 END) AS rest
FROM transactions
WHERE type = -1
  AND strftime('%Y', created_at) IS NOT NULL
GROUP BY strftime('%Y', created_at)
ORDER BY year DESC";

$stmt = $connection->query($sql);
if (!$stmt) {
    throw new Exception($connection->errorInfo()[2]);
}

return $stmt->fetchAll(PDO::FETCH_ASSOC);
