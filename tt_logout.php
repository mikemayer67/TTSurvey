<?php
session_start();
unset($_SESSION['USER_ID']);
setcookie('_tt_uid', '', 0, '/', '.'.$_SERVER['SERVER_NAME'], false, true);

$host = $_SERVER['SERVER_NAME'];
$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('_logout','',$uri);

header("Location: http://$host$uri");
?>
