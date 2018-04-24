<?php
session_start();

require_once(dirname(__FILE__).'/tt_init.php');

unset($_SESSION['USER_ID']);
unset($_SESSION['ANON_ID']);
unset($_SESSION['YEAR']);

setcookie('_tt_uid', '', 0, '/', '.'.$_SERVER['SERVER_NAME'], false, true);

header("Location: $tt_root_url/tt.php");
?>
