<?php

session_start();

require_once('db.php');

$returnCode = '404 Not Found';

if( isset( $_REQUEST['user_id'] ) )
{
  $user_id = strtoupper( $_REQUEST['user_id'] );

  $returnCode = '404 UserID Not Found';
  if( $db = db_connect() )
  {
    $sql = "select user_id from participants where user_id='$user_id'";
    if( $result = $db->query($sql) ) 
    {
      if ( $result->num_rows > 0 )
      {
        $returnCode = '200 Valid UserID';
      }
      $result->close();
    }
    else
    {
      error_log(__FILE__ .":: Invalid query at line " . __LINE__ .": $sql");
      $returnCode = '500 Server Error';
    }
    $db->close();
  }
}

header($_SERVER["SERVER_PROTOCOL"] . " " . $returnCode);

?>
