<?php

require_once(dirname(__FILE__).'/../tt_init.php');

try
{
  if( ! isset($_SESSION['USER_ID'] ) )
  {
    throw new Exception('Session missing user ID',500);
  }

  if( ! isset($_REQUEST['keys'] ) )
  {
    throw new Exception('keys not specified',404);
  }

  if( ! isset($_REQUEST['value'] ) )
  {
    throw new Exception('value not specified',404);
  }

  $user_id = $_SESSION['USER_ID'];
  $keys    = $_REQUEST['keys'];
  $value   = $_REQUEST['value'];

  $nkeys = count($keys);

  switch($keys[0])
  {
  case 'item':
    switch( $nkeys )
    {
    case 2:
      db_update_role($tt_year,$user_id, $keys[1], $value);
      break;

    case 3:
      db_update_role_option($tt_year,$user_id, $keys[1], $keys[2], $value);
      break;

    default:
      throw new Exception('improper set of keys provided for item',500);
    }

    break;

  case 'comment':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for comment',500);
    }
    db_update_group_comment($tt_year,$user_id,$keys[1], $value);
    break;

  case 'qual':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for qual',500);
    }
    db_update_role_qualifier($tt_year,$user_id,$keys[1], $value);
    break;

  case 'freetext':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for freetext',500);
    }
    db_update_freetext($tt_year,$user_id,$keys[1], $value);
    break;

  case 'anon':

    throw new Exception('Anonymous fields should not be dynamically updated',500);
    break;
  }

  header($_SERVER['SERVER_PROTOCOL'].' 200 Item Updated');
}
catch(Exception $e)
{
  error_log('exception caught: '.$e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), 'Bad Query') ) );
}

?>
