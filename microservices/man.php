<?php
global $connection;

$connection = new PDO('sqlite:db/main.db');

return require path()[1] . '.php';
