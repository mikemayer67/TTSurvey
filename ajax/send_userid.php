<?php

require_once(dirname(__FILE__).'/../tt_init.php');
require_once(dirname(__FILE__).'/../sendmail.php');

try
{
  if( ! isset($_SESSION['tta_passwd'] ) )
  {
    throw new Exception('Admin password not specified',401);
  }

  if( ! isset($_REQUEST['userid'] ) )
  {
    throw new Exception('Request is missing userid',400);
  }

  $userid = $_REQUEST['userid'];
  
  $user_info = db_user_info($userid);
  $name = $user_info['name'];
  $last_reminder = $user_info['reminder'];

  $email = $user_info['email'];
  if( ! ( isset($email) && strlen($email) > 0 ) )
  {
    throw new Exception("$name has no email address",418);
  }

  $now = time();

  if( ! is_null($last_reminder) )
  {
    error_log("asup: $name ($last_reminder) $now");
    if( $now < $last_reminder + 86400 )
    {
      throw new Exception("Reminder to $name sent less than 1 day ago", 418);
    }
  }
  error_log("asup: $name ok to send");

  if( ! email_welcome_info($userid,$name,$user_info['email'],$tt_active_year) )
  {
    throw new Exception("Failed to send email to $name", 420);
  }

  $reply = array('time' => date('M j \a\t h:i a', $now));

  header($_SERVER['SERVER_PROTOCOL'].' 200 User ID sent');
  header('Content-Type: application/json');
  echo json_encode($reply);
}
catch(Exception $e)
{
  error_log("ajax_send_userid failed: " . $e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
