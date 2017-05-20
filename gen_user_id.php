<?php


function gen_user_id($max_attempts)
{
  $pool = '123456789123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
  $npool = strlen($pool);

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
    error_log(__FILE__.":: candidate: $id");

    if( ! db_userid_exists($id) ) { return $id; }
  }

  throw new Exception("Failed to generate a unique ID in $max_attempts attempts", 500);
}

?>
