<?php

session_start();

require_once(dirname(__FILE__).'/db.php');

try
{
  if( ! isset($_REQUEST['user_id'] ) )
  {
    throw new Exception('User ID Not Specified',404);
  }

  $user_id = $_REQUEST['user_id'];

  $data = db_user_info($user_id);

  if( count($data) == 0 )
  {
    throw new Exception('User Not Found in Database',404);
  }

  header($_SERVER['SERVER_PROTOCOL'].' 200 Valid UserID');
  header('Content-Type: application/json');
  echo json_encode($data);
}
catch(Exception $e)
{
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())) );
}

?>
