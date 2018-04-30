<?php

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/sendmail.php");

try
{
  if( ! isset($_SESSION['USER_ID'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  if( ! isset($_REQUEST['year'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  $year = $_REQUEST['year'];

  $sent = array();
  $failed = array();

  $user_info = db_userid_admin();

  foreach ( $user_info as $info )
  {
    $email = $info['email'];
    $name = $info['name'];
    if( is_null($email) || strlen($email)==0 )
    {
      $noemail[] = $name;
    }
    else
    {
      if( email_welcome_info($info['id'],$name,$email,$year) )
      {
        $sent[] = "$name<$email>";
      }
      else
      {
        $failed[] = "$name<$email>";
      }
    }
  }

  header($_SERVER['SERVER_PROTOCOL'].' 200 User IDs sent');
  header('Content-Type: application/json');

  $timestamp = date('l, F j, Y \a\t h:i:s a', time());

  db_update_statics_userids_sent();

  $reply = array('sent'=>$sent, 'noemail'=>$noemail, 'failed'=>$failed, 'timestamp'=>$timestamp);
  echo json_encode($reply);
}
catch(Exception $e)
{
  error_log("ajax_send_all_userids failed: " . $e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
