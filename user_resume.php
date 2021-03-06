<?php

if ( ! isset( $_REQUEST['user_id'] ) )
{
  throw new Exception('Invoked without user_id in \$_REQUEST',404);
}

$user_id  = strtoupper($_REQUEST['user_id']);

if( db_verify_userid($user_id) )
{
  $_SESSION['USER_ID'] = $user_id;
  setcookie('_tt_uid', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);

  header("Location: $tt_root_url/tt.php");
}
else
{
  $err = "Invalid user_id provided ($user_id)";

  error_log(__FILE__.":: $err");
  $tt_error = "Invalid User ID ($user_id) entered";
  require("$dir/user_id_prompt.php");
}

?>

