<?php

function gen_user_id()
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

    if( ! db_userid_exists($id) ) { return $id; }
  }

  throw new Exception("Failed to generate a unique ID in $max_attempts attempts", 500);
}

?>
