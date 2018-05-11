<?php

require_once(dirname(__FILE__).'/../tt_init.php');
require_once(dirname(__FILE__).'/../sendmail.php');

try
{
  if( ! isset($_SESSION['tta_passwd'] ) )
  {
    throw new Exception('Admin password not specified',500);
  }

  if( ! isset($_REQUEST['year'] ) )
  {
    throw new Exception('Request is missing year',500);
  }

  $year = $_REQUEST['year'];
  $now = time();

  $reply = array( 'sent' => array(), 'failed'=>array(), 'noemail'=>array(), 'toosoon'=>array(), 'sentids' => array() );

  $user_info = db_all_participants();

  foreach ( $user_info as $info )
  {
    $id            = $info['id'];
    $email         = $info['email'];
    $name          = $info['name'];
    $last_reminder = $info['reminder'];

    $status = 'failed';
    if( is_null($email) || strlen($email)==0 )
    {
      $status = 'noemail';
    }
    else if( is_null($last_reminder) || $now > $last_reminder + $tta_reminder_frequency )
    {
      if( email_welcome_info($id,$name,$email,$year) ) 
      {
        $status = 'sent';
        $reply['sentids'][] = $id;
      }
    }
    else
    {
      $status = 'toosoon';
    }

    $reply[$status][] = $name;
  }

  db_update_statics('welcome_sent',$now);

  $reply['time'] = date('M j \a\t h:i a', $now);

  header($_SERVER['SERVER_PROTOCOL'].' 200 User IDs sent');
  header('Content-Type: application/json');
  echo json_encode($reply);
}
catch(Exception $e)
{
  error_log("ajax_send_all_userids failed: " . $e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
