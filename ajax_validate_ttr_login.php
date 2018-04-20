<?php

session_start();

require_once(dirname(__FILE__).'/db.php');

$statics = db_active_survey_statics();
$result_pwd = $statics['result_pwd'];

$valid_password = false;

if( isset($_REQUEST['value'] ) )
{
  $passwd = $_REQUEST['value'];

  $valid_password = ( strcmp( $passwd, $result_pwd) == 0 );
}
  
$data = array( 'valid' => $valid_password );

header($_SERVER['SERVER_PROTOCOL'].' 200 Valid Password');
header('Content-Type: application/json');
echo json_encode($data);

?>
