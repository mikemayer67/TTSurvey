<?php

require_once("$dir/gen_user_id.php");

$db = db_connect();

db_clear_unsubmitted($db,$tt_year,$user_id);

if( isset($_SESSION['ANON_ID']) )
{
  $anon_id = $_SESSION['ANON_ID'];
  db_clear_unsubmitted($db,$tt_year,$anon_id);
}

foreach ( $_POST as $key => $value )
{
  $keys  = split('_', $key);
  $nkeys = count($keys);

  $anon_key = "anon_$key";
  if( isset($_POST[$anon_key]) )
  {
    if( ! isset($anon_id) ) { 
      $anon_id = gen_anon_id($db); 
    }

    $id = $anon_id;
  }
  else
  {
    $id = $user_id;
  }

  switch($keys[0])
  {
  case 'item':
    switch($nkeys)
    {
    case 2:
      db_update_role($db,$tt_year,$user_id,$keys[1], 1);
      break;
    case 3:
      db_update_role_option($db,$tt_year,$user_id,$keys[1], $keys[2], 1);
      break;

    default:
      throw new Exception('improper set of keys provided for item',500);
    }
    break;

  case 'comment':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for comment',500);
    }
    db_update_group_comment($db,$tt_year,$id,$keys[1], $value);
    break;

  case 'qual':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for qual',500);
    }
    db_update_role_qualifier($db,$tt_year,$user_id,$keys[1], $value);
    break;

  case 'freetext':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for freetext',500);
    }
    db_update_freetext($db,$tt_year,$id,$keys[1], $value);
    break;
  }
}

db_promote($db,$tt_year,$user_id);

if( isset($anon_id) ) {
  db_promote($db,$tt_year,$anon_id); 
}

throw new Exception('gotcha..',500);

?>
