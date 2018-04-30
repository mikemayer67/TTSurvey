<?php

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/sendmail.php");

try
{
  if( ! isset($_SESSION['tta_passwd'] ) )
  {
    throw new Exception('Admin password not specified',500);
  }

  if( ! isset($_REQUEST['userid'] ) )
  {
    throw new Exception('Request is missing userid',500);
  }

  $userid = $_REQUEST['userid'];
  
  $user_info = db_user_info($userid);
  $name = $user_info['name'];

  $last_reminder = db_user_last_reminder($userid);

  if( ! is_null($last_reminder) )
  {
    $now = time();
    if( $now < $last_reminder + 86400 )
    {
      throw new Exception("Reminder to $name sent less than 1 day ago", 418);
    }
  }

  if( ! email_welcome_info($userid,$name,$user_info['email'],$tt_active_year) )
  {
    throw new Exception("Failed to send email to $name", 420);
  }

  header($_SERVER['SERVER_PROTOCOL'].' 200 User ID sent');
  header('Content-Type: application/json');
}
catch(Exception $e)
{
  error_log("ajax_send_userid failed: " . $e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
