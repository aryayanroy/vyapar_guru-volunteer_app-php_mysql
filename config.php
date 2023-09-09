<?php
$host = "localhost";
$dbname = "vyaparguru";
$user = "root";
$password = "";

try{
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Database connection failed: " . $e->getMessage();
    die();
}
?>