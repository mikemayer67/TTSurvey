<?php

function db_connect()
{
  $db_user = 'ctsluthe_ttadm';
  $db_pass = 'ctsluthe_ttadm';
  $db_name = 'ctsluthe_ttsurvey';
  $db_host = 'localhost';

  $db = mysqli_connect($db_host,$db_user,$db_pass,$db_name);

  if( ! $db ) { throw new Exception('Failed to connect to database',500);
  }

  return $db;
}


function db_userid_exists($id)
{
  try
  {
    $db = db_connect();

    $id = strtoupper($id);

    $sql = "select user_id from participants where user_id='$id'";
    $result = $db->query($sql);

    if( ! $result ) { throw new Exception("Invalid SQL: $sql",500);
    }

    $n = $result->num_rows;
    $result->close();
  }
  finally
  {
    $db->close();
  }

  return $n==1;
}

function db_user_info($id)
{
  $data = array();

  try
  {
    $db = db_connect();

    $id = strtoupper($id);

    $sql = "select name,email from participants where user_id='$id'";
    $result = $db->query($sql);

    if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

    $n = $result->num_rows;
    if( $n == 1 ) 
    { 
      $data = $result->fetch_assoc(); 
    }
    $result->close();
  }
  finally
  {
    $db->close();
  }

  return $data;
}
