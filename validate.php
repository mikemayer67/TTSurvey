<?php

session_start();

require_once('db.php');

$returnCode = '404 Not Found';
$returnData = Array();

if( isset( $_REQUEST['user_id'] ) )
{
  $user_id = strtoupper( $_REQUEST['user_id'] );

  $returnCode = '404 UserID Not Found';
  if( $db = db_connect() )
  {
    $sql = "select name,email from participants where user_id='$user_id'";
    if( $result = $db->query($sql) ) 
    {
      if ( $result->num_rows == 1 )
      {
        $returnCode = '200 Valid UserID';
        $returnData = $result->fetch_assoc();
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

if( sizeof($returnData) > 0 )
{
  header('Content-Type: application/json');
  echo json_encode($returnData);
}

?>
