<?php

session_start();

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/db.php");

try
{
  if( ! isset($_SESSION['tta_passwd'] ) )
  {
    throw new Exception('Admin password not specified',401);
  }

  if( ! isset($_REQUEST['user_id'] ) )
  {
    throw new Exception('User ID Not Specified',404);
  }

  if( ! isset($_REQUEST['user_email'] ) )
  {
    throw new Exception('User Email Not Specified',404);
  }

  $email   = $_REQUEST['user_email'];
  $user_id = $_REQUEST['user_id'];

  $rval = db_update_user_email($user_id, $email);
  error_log("Email for $user_id updated to $email");

  if( ! $rval ) { throw new Exception('User Not Found in Database',404); }

  header($_SERVER['SERVER_PROTOCOL'].' 200 Email Address Updated');
}
catch(Exception $e)
{
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
