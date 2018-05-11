<?php

require_once(dirname(__FILE__).'/db.php');
require_once(dirname(__FILE__).'/sendmail.php');

$nextPage = 'tt_survey';

try
{
  if(! isset($_REQUEST['user_name']))
  {
    throw new Exception('Invoked without user_name in \$_REQUEST',404);
  }
  if(! isset($_REQUEST['user_email']))
  {
    throw new Exception('Invoked without user_email in \$_REQUEST',404);
  }

  $name  = $_REQUEST['user_name'];
  $email = $_REQUEST['user_email'];

  $name = preg_replace('/^\s+/','',$name);
  $name = preg_replace('/\s+$/','',$name);
  $name = preg_replace('/\s+/',' ',$name);
  if( ! preg_match('/[a-z][a-z.]+(\s[a-z][a-z.]+)+$/i', $name) )
  {
    error_log(__FILE__ .":: Invalid user_name provided ($name)");

    if( strlen($name) > 0 ) 
    { 
      $tt_error = "You must provide both first and last name"; 
    }
    else
    {
      $tt_error = "You must provide your name"; 
    }
    throw new Exception('user_id_prompt',0);
  }

  $email = preg_replace('/^\s+/','',$email);
  $email = preg_replace('/\s+$/','',$email);
  if( ! preg_match('/^([^\s@]+@[^\s@]+.[^\s@]+)?$/', $email) )
  {
    error_log(__FILE__ .":: Invalid user_email provided ($email)");
    $tt_error = "Invalid Email ('$email') entered";
    throw new Exception('user_id_prompt',0);
  }

  $user_id = db_gen_user_id();

  error_log("New User ID Generated: $user_id  for: $name");

  db_add_new_participant($user_id, $name, $email, $tt_year);

  $_SESSION['USER_ID'] = $user_id;
  setcookie('_tt_uid', $user_id, time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);

  email_account_info($user_id,$name,$email,'');
}
catch (Exception $e)
{
  $code = $e->getCode();

  if( $code == 0 )
  {
    $nextPage = $e->getMessage();
  }
  else
  {
    throw $e;
  }
}

require(dirname(__FILE__)."/$nextPage.php");

?>
