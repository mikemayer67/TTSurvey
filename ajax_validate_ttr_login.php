<?php

session_start();

$valid_password = false;

if( isset($_REQUEST['value'] ) )
{
  $passwd = $_REQUEST['value'];

  $valid_password = ( strcmp( $passwd, 'CareteachservE') == 0 );
}
  
$data = array( 'valid' => $valid_password );

header($_SERVER['SERVER_PROTOCOL'].' 200 Valid Password');
header('Content-Type: application/json');
echo json_encode($data);

?>
