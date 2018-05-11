<?php

require_once(dirname(__FILE__).'/db.php');

if( ! isset($user_id) )
{
  throw new Exception('Loaded without \$user_id being set',404);
}

$data = db_get_user_info($user_id);

if( count($data) == 0 )
{
  if( isset($tt_link_was_used) && $tt_link_was_used )
  {
    $tt_error = 'Invalid User ID in the URL request';
  }
  require("$dir/user_id_prompt.php");
}
else
{
  $user_name = $data['name'];
  $user_email = $data['email'];

  require("$dir/user_id_confirm.php");
}

?>
