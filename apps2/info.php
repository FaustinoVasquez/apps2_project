<?php
$dsn = 'dblib:host=192.168.0.236;dbname=inventory';
$username = 'tempuser';
$password = 'pLa13t1B';

try {
    $dbh = new PDO($dsn, $username, $password);
    echo "Connected to the database successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>