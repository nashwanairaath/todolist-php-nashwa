<?php
$host = '127.0.0.1';
$dbname = 'todolist';
$username = 'root'; // ganti dengan username database mu
$password = ''; // ganti dengan password database mu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
