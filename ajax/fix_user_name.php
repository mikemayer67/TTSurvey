<?php

require_once(dirname(__FILE__).'/../tt_init.php');
require_once(dirname(__FILE__).'/../db.php');

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

  if( ! isset($_REQUEST['user_name'] ) )
  {
    throw new Exception('User Name Not Specified',404);
  }

  $name    = $_REQUEST['user_name'];
  $user_id = $_REQUEST['user_id'];

  $rval = db_update_user_name($user_id, $name);
  error_log("name for $user_id updated to $name");

  if( ! $rval ) { throw new Exception('User Not Found in Database',404); }

  header($_SERVER['SERVER_PROTOCOL'].' 200 User name Updated');
}
catch(Exception $e)
{
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
