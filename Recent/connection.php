<?php
$host = "localhost";
$dbname = "per_year_database_table"; 
$username = "root";
$password = "";

try {
    $con = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log($e->getMessage()); 
    echo "Connection failed";   
}
