<?php

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/sendmail.php");

try
{
  if( $tt_delog > 0 )
  {
    error_log('----------NEW TT-------------');

    error_log("HOST: " . $_SERVER['SERVER_NAME']);
    error_log(" URI: " . $_SERVER['REQUEST_URI']);
    error_log(" GET: " . count($_GET));
    error_log("POST: " . count($_POST));
    error_log(" REQ: " . count($_REQUEST));
    if( $tt_delog > 1 )
    {
      error_log("GET: " . print_r($_GET,true));
      error_log("POST: " . print_r($_POST,true));
      error_log("REQUEST: " . print_r($_REQUEST,true));
      error_log("SESSION: " . print_r($_SESSION,true));
      error_log("COOKIES: " . print_r($_COOKIE,true));
      error_log("HTTP HEADERS...");
      foreach (getallheaders() as $hkey => $hvalue) { error_log("$hkey: $hvalue"); }
    }
  }

  if( isset($_POST['action']) )
  {
    $action = strtolower($_POST['action']);
    $page = 'user_'.$action;
  }
  else if( isset($_POST['confirmed']) )
  {
    if(isset($_POST['cancel']))
    {
      $page = 'user_id_prompt';
      unset($_SESSION['USER_ID']);
      unset($_SESSION['ANON_ID']);
      setcookie('_tt_uid', '', 0, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    }
    else
    {
      $user_id = $_POST['confirmed'];

      if( ! db_verify_userid($user_id) ) 
      { 
        throw new Exception( "Invalid user_id provided ($user_id)", 404 );
      }

      $_SESSION['USER_ID'] = $user_id;

      if( isset($_REQUEST['anon_id']) )
      {
        $_SESSION['ANON_ID'] = $_REQUEST['anon_id'];
      }

      setcookie('_tt_uid', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
      $page = 'tt_survey';
    }

  }
  else if( isset($_REQUEST['uid']) )
  {
    $user_id = $_REQUEST['uid'];
    if( isset($_REQUEST['aid']) ) { $anon_id = $_REQUEST['aid']; }
    $tt_link_was_used = true;
    $page = 'user_verify';
  }
  else if( isset($_POST['submit_survey']) )
  {
    $user_id = $_POST['user_id'];

    if( ! ( isset($_SESSION['USER_ID']) && $user_id === $_SESSION['USER_ID'] ) )
    {
      error_log("Attempt to submit survey with invalid or mismateched user ID: \n  POST=$user_id  SESSION=".$_SESSION['USER_ID']);
      throw new Exception('Invalid user', 500);
    }
    $page = 'tt_survey_submitted';
  }
  else if( isset($_SESSION['USER_ID']) )
  {
    $user_id = $_SESSION['USER_ID'];
    setcookie('_tt_uid', $_SESSION['USER_ID'], time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    $page = 'tt_survey';
  }
  else if (isset($_COOKIE['_tt_uid']) )
  {
    $user_id = $_COOKIE['_tt_uid'];
    $page = 'user_verify';
  }
  else
  {
    $page = 'user_id_prompt';
  }

  if( $tt_delog > 0 ) { error_log("Loading: $dir/$page.php"); }

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
