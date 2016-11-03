<?php
if(isset($_SESSION["server"])) {
    unset($_SESSION["server"]);
}

if(isset($_SESSION["user"])) {
    unset($_SESSION["user"]);
}

if(isset($_SESSION["password"])) {
    unset($_SESSION[""]);
}

if(isset($_SESSION["port"])) {
    unset($_SESSION["port"]);
}

header("location:index");