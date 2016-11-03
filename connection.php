<link rel="stylesheet" href="style.default.css"/>
<script src="jquery.min-1.12.4.js"></script>

<?php
session_start();
//ini_set("display_errors",0);
include_once "db-sqllite.php";

$message = "";
$description = "";
$server = "localhost";
$user = "root";
$password = "";
$port = 3306;

if (isset($_POST["test_connection"]) || isset($_POST["add_connection"])) {

    $description = $_POST["server_description"];
    $server = $_POST["server"];
    $user = $_POST["user"];
    $password = $_POST["password"];
    $port = $_POST["port"];

    $dsn = "mysql:host=$server";
    try {
        $dbh = new PDO($dsn, $user, $password, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $message = "<span style='color: green'>Connection Success</span>";
        if (isset($_POST["add_connection"])) {
            $sql = "DELETE FROM connection_setting WHERE server = '$server'";
            $data_generator_con->exec($sql);

            $sql = "INSERT INTO connection_setting
              (server, user, password, port, server_description) 
              VALUES ('$server','$user','$password','$port','$description');";

            $data_generator_con->exec($sql);
            $message = "<span style='color: green'>Successfully connection added</span>";
        }

    } catch (PDOException $e) {
        $message = "<span style='color: red'>Unable to connect</span>";
    }
} elseif (isset($_GET["mode"]) && isset($_GET["server"]) && $_GET["mode"] == "delete") {
    $sql = "DELETE FROM connection_setting WHERE server='" . $_GET["server"] . "'";
    //echo $sql;
    $data_generator_con->exec($sql);
    $message = "<span style='color: green'>Connection successfully deleted</span>";
}
$i = 1;
?>
<div align="center">
    <p>Connect to Database Host</p>
    <?php if (isset($_SESSION["message"])) { ?>
        <p style="color: red"><?php echo $_SESSION["message"]; ?></p>
        <?php unset($_SESSION["message"]) ?>
    <?php } ?>
    <div class="connection-table">
        <table>
            <tr>
                <td>#</td>
                <td>Description</td>
                <td>Server</td>
                <td>User</td>
                <td>Password</td>
                <td>Port</td>
                <td>Action</td>
            </tr>
            <?php $result = $data_generator_con->query('SELECT * FROM connection_setting'); ?>
            <?php foreach ($result as $row) { ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $row['server_description'] ?></td>
                    <td><?php echo $row['server'] ?></td>
                    <td><?php echo $row['user'] ?></td>
                    <td>*****</td>
                    <td><?php echo $row['port'] ?></td>
                    <td style="min-width: 200px;">
                        <a href="connection?mode=delete&server=<?php echo $row["server"] ?>" class="btn">Delete</a> |
                        <a href="list-database?server=<?php echo $row["server"] ?>"
                           onclick="ajax_loader(this)" class="btn">Connect</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <p>Add/Edit Connection</p>
    <div class="connection-add">
        <form method="post">
            <table>
                <tr>
                    <td>Server</td>
                    <td><input type="text" name="server" value="<?php echo $server ?>"/></td>
                </tr>
                <tr>
                    <td>User</td>
                    <td><input type="text" name="user" value="<?php echo $user ?>"/></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" value="<?php echo $password ?>"/></td>
                </tr>
                <tr>
                    <td>Port</td>
                    <td><input type="text" name="port" value="<?php echo $port ?>"/></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><input type="text" name="server_description" value="<?php echo $description ?>"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="test_connection" value="Test" class="btn"/>
                        <input type="submit" name="add_connection" value="Save" class="btn"/></td>
                </tr>
                <?php if ($message != "") { ?>
                    <tr>
                        <td colspan="2"><?php echo $message ?></td>
                    </tr>
                <?php } ?>
            </table>
        </form>
    </div>
</div>

<script>
    function ajax_loader(thisObj) {
        $('body').after("<div class='ajax-loader'></div>")
    }
</script>


