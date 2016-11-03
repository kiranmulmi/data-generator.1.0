<?php
/**
 * Created by PhpStorm.
 * User: Kiran
 * Date: 10/27/2016
 * Time: 7:21 AM
 */

$host = "(local)";
$database = "db_kiran";
$user = "sa";
$pass = "kiran";


$dbh = new PDO("dblib:host=$host;dbname=$database", $user, $pass);