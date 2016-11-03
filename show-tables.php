<?php

session_start();
include_once "includes-all.php";

$dbname = $_GET["database"];

$conn = get_connection($dbname);

$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = $result->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="header">
    <div class="logo"><b>Data Generator v1.0</b></div>
    <div class="breadcrumb"><a href="list-database?server=<?php echo $_SESSION["server"] ?>">Home</a> / <?php echo $dbname ?> /</div>
    <div style="width: 30px;float: right;margin-right: 60px;"><a href="logout">Logout</a></div>
</div>

<div class="left-nav">
    <ol>
        <?php foreach ($tables as $table) { ?>
            <li><a href='show-columns?database=<?php echo $dbname . "&table=" . $table ?>'><?php echo $table ?></a>
            </li>
        <?php } ?>
    </ol>
</div>

<div class="main-body">
</div>
