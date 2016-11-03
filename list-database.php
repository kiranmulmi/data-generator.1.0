<link rel="stylesheet" href="style.default.css"/>
<script src="jquery.min-1.12.4.js" type="text/javascript"></script>

<?php
$server = $_GET["server"];
session_start();
include_once "db-sqllite.php";
$sqlQuery = $data_generator_con->query("SELECT * FROM connection_setting WHERE server='$server'");
$result = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
if(isset($result[0])) {
    $_SESSION["server"] = $result[0]["server"];
    $_SESSION["user"] = $result[0]["user"];
    $_SESSION["password"] = $result[0]["password"];
    $_SESSION["port"] = $result[0]["port"];
}
include_once "db-connection.php";

$sql = "show databases";

$sqlQuery = $db->query($sql);
$databases = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
$database_sorted = array();
foreach ($databases as $database) {
    $database_sorted[] = $database["Database"];
}

sort($database_sorted);

$not_allowed_db = array("information_schema", "performance_schema", "sys","data_generator","mysql");
?>

<div align="center" class="database-list">
    <p>Databases in <?php echo $server ?></p>
    <div><ul><li><a href="logout" style="padding: 4px; background-color: #afc385; color: white;">Logout</a></ul><li></div>
    <ul>
        <?php foreach ($database_sorted as $database) { ?>
            <?php if (!in_array($database, $not_allowed_db)) { ?>
                <li>
                    <a href='show-tables?database=<?php echo $database ?>' > <?php echo $database ?></a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>


</div>
