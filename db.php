<?php

function db_connect()
{
  $db_user = 'ctsluthe_ttadm';
  $db_pass = 'ctsluthe_ttadm';
  $db_name = 'ctsluthe_ttsurvey';
  $db_host = 'localhost';

  $db = mysqli_connect($db_host,$db_user,$db_pass,$db_name);

  if( ! $db ) { throw new Exception('Failed to connect to database',500); }

  if( ! $db->set_charset('utf8') ) { throw new Exception('Failed to use charset utf8',500); }

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

function db_update_user_name($id,$name)
{
  $rval = false;
  try
  {
    $db = db_connect();

    $id = strtoupper($id);

    $sql = "update participants set name='$name' where user_id='$id'";
    $rval = $db->query($sql);
  }
  finally
  {
    $db->close();
  }

  return $rval;
}

function db_update_user_email($id,$email)
{
  $rval = false;
  try
  {
    $db = db_connect();

    $id = strtoupper($id);

    $sql = "update participants set email='$email' where user_id='$id'";
    $rval = $db->query($sql);
  }
  finally
  {
    $db->close();
  }

  return $rval;
}

function db_survey_groups($db,$year)
{
  $data = array();

  $sql = "select * from survey_groups where year=$year order by group_index";
  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

  while( $row = $result->fetch_assoc() )
  {
    $data[] = $row;
  }

  $result->close();

  return $data;
}


function db_survey_items($db,$year,$group)
{
  $data = array();

  $sql = "
select si.order_index,
       si.item_type,
       si.qualification_label,
       si.note,
       si.anonymous,
       sl.type,
       sl.level,
       sl.italic,
       sl.bold,
       sl.size,
       si.note,
       coalesce(sl.value,si.label) label
       from survey_items si left join survey_labels as sl
   on si.item_id = sl.item_id 
where si.year = $year
  and si.group_index = $group;";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

  while( $row = $result->fetch_assoc() )
  {
    error_log(print_r($row,true));
    $data[] = $row;
  }

  $result->close();

  return $data;
}
