<?php

function db_connect()
{
  $db_user = 'ctsluthe_ttadm';
  $db_pass = 'ctsluthe_ttadm';
  $db_name = 'ctsluthe_ttsurvey';
  $db_host = 'localhost';

  $db = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
  if ( ! $db )
  {
    error_log("Failed to connect to $db_user/$db_pass@$db_host.$db_name: ".$db->connect_error);
    $db->close();
  }
  else
  {
    return $db;
  }
}

