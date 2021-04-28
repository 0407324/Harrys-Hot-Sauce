<?php
error_reporting(E_ERROR | E_PARSE);
include 'includes/Harrys_DB_Connection.php';
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_dbname, $db_port);
mysqli_select_db($link, $db_dbname);
?>