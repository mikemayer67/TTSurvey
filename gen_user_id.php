<?php

function gen_user_id($db)
{
  $pool = '123456789123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
  $npool = strlen($pool);

  $max_attempts = 256;

  for($attempt=0; $attempt<$max_attempts; ++$attempt)
  {
    $keys = array();
    for( $i=0; $i<12; $i++)
    {
      if( $i>0 && $i % 3 == 0 ) { 
        $keys[] = '-'; 
      }

      $keys[] = substr($pool,rand(0,$npool-1),1);
    }
    $id = implode($keys);

    if( ! db_userid_exists($id,$db) ) { return $id; }
  }

  throw new Exception("Failed to generate a unique ID in $max_attempts attempts", 500);
}

function gen_anon_id($db)
{
  $anon_id = gen_user_id($db);

  $sql = "insert into user_ids values ('$anon_id')";
  $result = $db->query($sql);
  if( ! $result )
  {
    throw new Exception("Invalid SQL: $sql",500);
  }

  return $anon_id;
}

?>
