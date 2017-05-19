<?php


function gen_user_id()
{
  $pool = '123456789123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
  $npool = strlen($pool);

  $keys = array();
  for( $i=0; $i<12; $i++)
  {
    if( $i>0 && $i % 3 == 0 ) { 
      $keys[] = '-'; 
    }

    $keys[] = substr($pool,rand(0,$npool-1),1);
  }
  $id = implode($keys);

  return $id;
}

?>
