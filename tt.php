<?php

$dir = dirname(__FILE__);

$tt_delog = 1;

error_log('----------NEW TT-------------');

require_once("$dir/tt_init.php");

try
{
  error_log("HOST: " . $_SERVER['SERVER_NAME']);
  error_log(" URI: " . $_SERVER['REQUEST_URI']);
  error_log(" GET: " . count($_GET));
  error_log("POST: " . count($_POST));
  error_log(" REQ: " . count($_REQUEST));
  if( $tt_delog > 0 )
  {
    error_log("GET: " . print_r($_GET,true));
    error_log("POST: " . print_r($_POST,true));
    error_log("REQUEST: " . print_r($_REQUEST,true));
    error_log("SESSION: " . print_r($_SESSION,true));
    error_log("COOKIES: " . print_r($_COOKIE,true));
    error_log("HTTP HEADERS...");
    foreach (getallheaders() as $name => $value) { error_log("$name: $value"); }
  }
  error_log("NOJS: " . ((isset($tt_nojs) && $tt_nojs) ? "YES" : "NO"));

  $page;

  if( isset($_REQUEST['action']) )
  {
    $action = strtolower($_REQUEST['action']);
    $page = 'user_'.$action;
  }
  else if( isset($_REQUEST['confirmed']) )
  {
    if(isset($_REQUEST['cancel']))
    {
      $page = 'user_id_prompt';
      unset($_SESSION['USER_ID']);
      setcookie('USER_ID', '', 0, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    }
    else
    {
      $user_id = $_REQUEST['confirmed'];

      if( ! db_userid_exists($user_id) ) 
      { 
        throw new Exception( "Invalid user_id provided ($user_id)", 404 );
      }

      $_SESSION['USER_ID'] = $user_id;
      setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
      $page = 'survey';
    }

  }
  else if( isset($_REQUEST['uid']) )
  {
    $user_id = $_REQUEST['uid'];
    $tt_link_was_used = true;
    $page = 'user_verify';
  }
  else if( isset($_SESSION['USER_ID']) )
  {
    $user_id = $_SESSION['USER_ID'];
    setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    $page = 'survey';
  }
  else if (isset($_COOKIE['USER_ID']) )
  {
    $user_id = $_COOKIE['USER_ID'];
    $page = 'user_verify';
  }
  else
  {
    $page = 'user_id_prompt';
  }

  error_log("Loading: $dir/$page.php");
  require("$dir/$page.php");
}
catch (Exception $e)
{
  $code = $e->getCode();

  $msg  = $e->getMessage();
  $file = $e->getFile();
  $line = $e->getLine();

  error_log("$file\[$line\]: $msg");
  require("$dir/$code.php");
}


?>
