<?php

require_once(dirname(__FILE__).'/tt_init.php');
require_once(dirname(__FILE__).'/gen_user_id.php');

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

  if( isset($_REQUEST['anon']) )
  {
    $anon = $_REQUEST['anon'];
  }
  else
  {
    $anon = false;
  }

  $user_id = $_SESSION['USER_ID'];
  $keys    = $_REQUEST['keys'];
  $value   = $_REQUEST['value'];

  $nkeys = count($keys);

  error_log(__FILE__ . ':: ' . $user_id . "\n   anon: $anon\n  value: " . $value . "\n   keys: " . print_r($keys,true));

  $db = db_connect();

  if($anon || $keys[0] === 'anon')
  {
    if( ! isset( $_SESSION['ANON_ID'] ) )
    {
      $_SESSION['ANON_ID'] = gen_anon_id($db);
      error_log('New anonymous ID: '.$_SESSION['ANON_ID']);
    }

    $anon_id = $_SESSION['ANON_ID'];
  }

  switch($keys[0])
  {
  case 'item':
    switch( $nkeyes )
    {
    case 2:
      db_update_role($db,$tt_year,$user_id, $keys[1], $value, 0);
      break;

    case 3:
      db_update_role_option($db,$tt_year,$user_id, $keys[1], $keys[2], $value, 0);
      break;

    default:
      throw new Exception('improper set of keys provided for item',500);
    }

    break;

  case 'comment':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for comment',500);
    }
    $id = $user_id;
    if($anon) { $id = $anon_id; }
    db_update_group_comment($db,$tt_year,$id,$keys[1], $value, 0);
    break;

  case 'qual':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for qual',500);
    }
    db_update_role_qualifier($db,$tt_year,$user_id,$keys[1], $value, 0);
    break;

  case 'freetext':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for freetext',500);
    }
    $id = $user_id;
    if($anon) { $id = $anon_id; }
    error_log("$id $anon $anon_id $user_id");
    db_update_freetext($db,$tt_year,$id,$keys[1], $value, 0);
    break;

  case 'anon':

    if( $nkeys < 2 ) {
      throw new Exception('too few keys provided from anon',500);
    }

    $from_id = ($value ? $user_id : $anon_id);
    $to_id   = ($value ? $anon_id : $user_id);

    switch($keys[1])
    {
    case 'comment':
      if( $nkeys != 3 ) {
        throw new Exception('improper set of keys provided for anon comment',500);
      }
      db_transfer_group_comment($db,$tt_year,$from_id,$to_id,$keys[2],0);
      break;
    case 'freetext':
      if( $nkeys != 3 ) {
        throw new Exception('improper set of keys provided for anon freetext',500);
      }
      db_transfer_freetext($db,$tt_year,$from_id,$to_id,$keys[2],0);
      break;
    default:
      throw new Exception('item type $keys[1] cannot be anonymous',500);
    }

    break;
  }


  header($_SERVER['SERVER_PROTOCOL'].' 200 Item Updated');
}
catch(Exception $e)
{
  error_log('exception caught: '.$e->getMessage());
  header( implode(' ', array( $_SERVER['SERVER_PROTOCOL'], $e->getCode(), 'Bad Query') ) );
}
finally
{
  $db->close();
}


?>
