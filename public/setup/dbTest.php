<?php
$servername = $_POST["host"];
$dbname = $_POST["dbname"];
$driver_pdo = $_POST["driver_pdo"];
$username = $_POST["username"];
$password = $_POST["password"];
$port = $_POST["port"];
try {
    $conn = new \PDO("$driver_pdo:host=$servername;port=$port;dbname=$dbname", $username, $password);
    // set the \PDO error mode to \Exception
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    echo "success";
}
catch(\PDOException $e)
{
    echo $e->getMessage();
}
?>