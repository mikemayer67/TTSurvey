<?php

require_once('tt_init.php');

if( isset($_SESSION['USER_ID']) )
{
  error_log('case 1');
  setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400);
  require(dirname(__FILE__).'/survey.php');
}
else if (isset($_COOKIE['USER_ID']) )
{
  error_log('case 2');
  require(dirname(__FILE__).'/user_id_verify.php');
}
else
{
  error_log('case 3');
  require(dirname(__FILE__).'/user_id_prompt.php');
}

?>
