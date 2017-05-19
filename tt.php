<?php

require_once(dirname(__FILE__).'/tt_init.php');

if( isset($_REQUEST['action']) )
{
  $action = strtolower($_REQUEST['action']);
  if( $action === 'start' )
  {
    require(dirname(__FILE__).'/user_start.php');
  }
  else if( $action === 'resume' )
  {
    require(dirname(__FILE__).'/user_resume.php');
  }
  else
  {
    require(dirname(__FILE__).'/404.php');
  }
}
else if( isset($_SESSION['USER_ID']) )
{
  setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400);
  require(dirname(__FILE__).'/survey.php');
}
#else if (isset($_COOKIE['USER_ID']) )
#{
#  require(dirname(__FILE__).'/user_id_verify.php');
#}
else
{
  require(dirname(__FILE__).'/user_id_prompt.php');
}

?>
