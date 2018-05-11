<?php

require_once(dirname(__FILE__).'/../tt_init.php');

try
{
  if( ! isset($_SESSION['USER_ID'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  $user_id = $_SESSION['USER_ID'];
  $anon_id = $_SESSION['ANON_ID'];

  $db = db_connect();

  db_clear($db,$tt_year,$user_id,0);
  $data = db_retrieve_user_responses($tt_year,$user_id,$anon_id);

  header($_SERVER['SERVER_PROTOCOL'].' 200 Reload Data');
  header('Content-Type: application/json');
  echo json_encode($data);
}
catch(Exception $e)
{
  error_log('exception caught: '.$e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), $e->getMessage())));
}
finally
{
  $db->close();
}
?>
