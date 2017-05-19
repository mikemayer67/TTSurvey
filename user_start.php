<?php

require_once(dirname(__FILE__).'/db.php');
require_once(dirname(__FILE__).'/gen_user_id.php');

if(! isset($_REQUEST['user_name']))
{
  if(! isset($_REQUEST['user_email']))
  {
    $missing_arg = "either user_name or user_email";
  }
  else
  {
    $mssing_arg = "user_name";
  }
}
else if(! isset($_REQUEST['user_name']))
{
  $mssing_arg = "user_email";
}

if( isset($missing_arg) )
{
  error_log(__FILE__.":: Invoked without $mssing_arg in \$_REQUEST");
  require(dirname(__FILE__).'/404.php');
  return;
}

$name  = $_REQUEST['user_name'];
$email = $_REQUEST['user_email'];

$name = preg_replace('/^\s+/','',$name);
$name = preg_replace('/\s+$/','',$name);
$name = preg_replace('/\s+/',' ',$name);
if( ! preg_match('/[a-z][a-z.]+(\s[a-z][a-z.]+)+$/i', $name) )
{
  error_log(__FILE__ .":: Invalid user_name provided ($name)");
  if( $tt_nojs )
  {
    if( strlen($name) > 0 ) 
    { 
      $tt_error = "You must provide both first and last name"; 
    }
    else
    {
      $tt_error = "You must provide your name"; 
    }
    require(dirname(__FILE__).'/user_id_prompt.php');
    return;
  }
  else
  {
    require(dirname(__FILE__).'/500.php');
    return;
  }
}

$email = preg_replace('/^\s+/','',$email);
$email = preg_replace('/\s+$/','',$email);
if( ! preg_match('/^([^\s@]+@[^\s@]+.[^\s@]+)?$/', $email) )
{
  error_log(__FILE__ .":: Invalid user_email provided ($email)");
  if( $tt_nojs )
  {
    $tt_error = "Invalid Email ('$email') entered";
    require(dirname(__FILE__).'/user_id_prompt.php');
    return;
  }
  else
  {
    require(dirname(__FILE__).'/500.php');
    return;
  }
}

$attempts = 0;

$db = db_connect();
if( ! $db )
{
  error_log(__FILE__.":: Failed to connect to database");
  require(dirname(__FILE__).'/500.php');
  return;
}


$found_id = false;
for( $i=0; $found_id == false && $i<$tt_max_gen_id_attempts; ++$i)
{
  $user_id = gen_user_id();

  error_log("candidate ID: $user_id");

  $sql = "select user_id from user_ids where user_id='$user_id'";

  $result = $db->query($sql);
  if( ! $result )
  {
    $db->close();
    error_log(__FILE__.":: Invalid SQL at line ".__LINE__.": $sql");
    require(dirname(__FILE__).'/500.php');
    return;
  }
  else if( $result->num_rows == 0 ) 
  { 
    $found_id = true;
  }
}
if( $found_id == false )
{
  $db->close();
  error_log(__FILE__.":: Failed to generate a unique ID in $tt_max_gen_id_attempts attempts");
  require(dirname(__FILE__).'/500.php');
  return;
}

error_log("New User ID Generated: $user_id  for: $name");

$sql = "insert into user_ids values ('$user_id')";
$result = $db->query($sql);
if( ! $result )
{
  error_log(__FILE__.":: Invalid SQL at line ".__LINE__.": $sql");
  require(dirname(__FILE__).'/500.php');
  return;
}

$sql = "insert into participants values ('$user_id','$name','$email')";
$result = $db->query($sql);
if( ! $result )
{
  error_log(__FILE__.":: Invalid SQL at line ".__LINE__.": $sql");
  require(dirname(__FILE__).'/500.php');
  return;
}

$sql = "insert into participation_history values ('$user_id',$tt_year,0)";
$result = $db->query($sql);
if( ! $result )
{
  error_log(__FILE__.":: Invalid SQL at line ".__LINE__.": $sql");
  require(dirname(__FILE__).'/500.php');
  return;
}

$_SESSION['user_id'] = $user_id;
setcookie('USER_ID', $_SESSION['user_id'], time()+30*86400);

require(dirname(__FILE__).'/survey.php');


