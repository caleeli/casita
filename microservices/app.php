<?php
global $connection;
$db = 'db/' . path()[1] . '.db';
$connection = new PDO('sqlite:' . $db);

return require path()[2] . '.php';
