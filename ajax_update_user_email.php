<?php

session_start();

$dir = dirname(__FILE__);

require_once("$dir/db.php");
require_once("$dir/sendmail.php");

try
{
  if( ! isset($_SESSION['USER_ID'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  if( ! isset($_REQUEST['user_email'] ) )
  {
    throw new Exception('User Email Not Specified',404);
  }

  $email   = $_REQUEST['user_email'];
  $user_id = $_SESSION['USER_ID'];

  $rval = db_update_user_email($user_id, $email);

  if( ! $rval ) { throw new Exception('User Not Found in Database',404); }

  $info = db_user_info($user_id);

  $anon_id = ( isset($_SESSION['ANON_ID']) ? $_SESSION['ANON_ID'] : '' );
  email_account_info($user_id, $info['name'], $email, $anon_id );

  header($_SERVER['SERVER_PROTOCOL'].' 200 Email Address Updated');
}
catch(Exception $e)
{
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}


?>
