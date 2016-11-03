<?php

if(!isset($_SESSION["server"]) && !isset($_SESSION["user"]) && !isset($_SESSION["password"])) {
    header("location:index.php");
    exit();
}

$host = $_SESSION["server"];
$user = $_SESSION["user"];
$password = $_SESSION["password"];
$port = $_SESSION["port"];

try {
    $db = new PDO("mysql:host=$host;port=$port", $user, $password);
} catch (PDOException $e) {
    $_SESSION["message"] = "Unable to connect to database server";
    header("location:index");
}

function get_connection($database) {
    global $host;
    global $user;
    global $password;
    global $port;

    return new PDO("mysql:host=$host;dbname=$database;port=$port", $user, $password );
}