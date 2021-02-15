<?php
$user = 'root';
$pw = 'root';
$host = '172.30.0.10';
$port = 3306;
$db = 'base';
try {
    $sql = new PDO (sprintf('mysql:dbname=%s;host=%s;port=%d;charset=utf8', $db, $host, $port), $user, $pw);
    echo 'connected to mysql.';
} catch (PDOException $e) {
    var_dump($e);
}