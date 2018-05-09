<?php

require_once("$dir/gen_user_id.php");
require_once("$dir/sendmail.php");

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

  $db = db_connect(); 

  $user_id = gen_user_id($db);

  error_log("New User ID Generated: $user_id  for: $name");

  try
  {
    db_record_new_participant($db, $user_id, $name, $email, $tt_year);
    
    $_SESSION['USER_ID'] = $user_id;
    setcookie('_tt_uid', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);

    email_account_info($user_id,$name,$email,'');
  }
  finally
  {
    $db->close();
  }
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

require("$dir/$nextPage.php");

?>
