<?php

require_once(dirname(__FILE__).'/../tt_init.php');
require_once(dirname(__FILE__).'/../db.php');

try
{
  if( ! isset($_SESSION['USER_ID'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  if( ! isset($_REQUEST['user_name'] ) )
  {
    throw new Exception('User Name Not Specified',404);
  }

  $user_id   = $_SESSION['USER_ID'];
  $user_name = $_REQUEST['user_name'];

  $rval = db_update_user_name($user_id, $user_name);

  if( ! $rval ) { throw new Exception('User Not Found in Database',404); }

  header($_SERVER['SERVER_PROTOCOL'].' 200 User Name Updated');
}
catch(Exception $e)
{
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}


?>
