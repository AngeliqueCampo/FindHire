<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "findhire_campo";

try {
    // setup PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // error message 
    die("Connection failed: " . $e->getMessage());
}
?>
