<?php


$db_host = "localhost:00007";
$db_name = "XYZ";
$db_user = "XYZ";
$db_pwd = "XYZ";

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);
$conn -> set_charset("utf8");
if(mysqli_connect_error()){
    echo mysqli_connect_error();
    exit;
}
?>