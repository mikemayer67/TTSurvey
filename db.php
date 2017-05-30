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


function db_userid_exists($id,$db=null)
{
  $id = strtoupper($id);

  if(is_null($db))
  {
    $tmp_db = true;
    $db = db_connect();
  }

  $sql = "select user_id from participants where user_id='$id'";
  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500);
  }

  $n = $result->num_rows;
  $result->close();

  if( $tmp_db ) { $db->close(); }

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

function db_update_participation_history($db,$year,$user_id,$submitted)
{
  $sql = "insert ignore into participation_history values ('$user_id',$year,$submitted)";
  error_log($sql);

  $result = $db->query($sql);
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
    select 
       si.item_id,
       si.order_index,
       si.item_type,
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
    $data[] = $row;
  }

  $result->close();

  return $data;
}

function db_role_options($db,$year)
{
  $rval = array();

  $sql = "
    select a.item_id,
           a.option_id,
           a.option_label,
           a.is_primary
      from survey_role_options a
     where exists ( select * from survey_items b
                     where a.item_id = b.item_id
                       and b.year = $year )
     order by item_id,option_id;";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

  while( $row = $result->fetch_row() )
  {
    list($item_id,$option_id,$option_label,$is_primary) = $row;

    $key = ( $is_primary ? 'primary' : 'secondary' );

    $rval[$item_id][$key][] = array( 'label' => $option_label,
                                     'id'    => $item_id.'_'.$option_id );
  }
  $result->close();

  return $rval;
}

function db_role_qualifiers($db,$year)
{
  $rval = array();

  $sql = "
    select concat(a.item_id,'_',a.qualification_option),
           a.qualification_hint
      from survey_role_qualifiers a
     where exists ( select * from survey_items b
                     where a.item_id = b.item_id
                       and b.year = $year )";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

  while( $row = $result->fetch_row() )
  {
    list($id,$hint) = $row;
    $rval[$id] = $hint;
  }
  $result->close();

  return $rval;
}

function db_role_dependencies($db,$year)
{
  $rval = array();

  $sql = "
    select concat(item_id,'_',option_id) as child,
           concat(item_id,'_',require_option_id) as parent 
      from survey_role_options a
     where a.is_primary=0
       and exists ( select * from survey_items b
                     where a.item_id = b.item_id
                       and b.year = $year ); ";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }

  while( $row = $result->fetch_row() )
  {
    list($child,$parent) = $row;

    $child = "#item_$child";

    if( isset($rval[$parent]) )
    {
      $rval[$parent] .= ",$child";
    }
    else
    {
      $rval[$parent] = $child;
    }
  }
  $result->close();

  return $rval;
}

function db_update_role($db,$year,$user_id,$item_id,$value,$submitted)
{
  db_update_participation_history($db,$year,$user_id,$submitted);

  $sql = "
    insert into response_role 
           (user_id, year, submitted, item_id, selected)
    values ('$user_id', $year, $submitted, $item_id, $value)
        on duplicate key update
           selected = $value;";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_update_role_option($db,$year,$user_id,$item_id,$option_id,$value,$submitted)
{
  db_update_participation_history($db,$year,$user_id,$submitted);

  db_update_role($db,$year,$user_id,$item_id,$value,$submitted);

  $sql = "
    insert into response_role_options
           (user_id, year, submitted, item_id, option_id, selected)
    values ('$user_id', $year, $submitted, $item_id, $option_id, $value)
        on duplicate key update
           selected = $value;";

  $result = $db->query($sql);

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_update_group_comment($db,$year,$user_id,$group_index,$value,$submitted)
{
  error_log('db_update_group_comment');
  db_update_participation_history($db,$year,$user_id,$submitted);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->real_escape_string($value);

  if( strlen($value) == 0 )
  {
    $sql = "
      delete from response_group_comment
       where user_id='$user_id'
         and year=$year
         and submitted=$submitted
         and group_index=$group_index;";

    error_log($sql);

    $result = $db->query($sql);
  }
  else
  {
    $sql = "
      insert into response_group_comment
            (user_id, year, submitted, group_index, text)
      values ('$user_id', $year, $submitted, $group_index, '$value')
          on duplicate key update
            text = '$value';";

    error_log($sql);
    $result = $db->query($sql);
  }

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_update_role_qualifier($db,$year,$user_id,$item_id,$value,$submitted)
{
  error_log('db_update_role_qualifier');
  db_update_participation_history($db,$year,$user_id,$submitted);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->real_escape_string($value);

  if( strlen($value) == 0 )
  {
    $sql = "
      delete from response_roles 
       where user_id='$user_id'
         and year=$year
         and submitted=$submitted
         and item_id=$item_id;";

    error_log($sql);

    $result = $db->query($sql);
  }
  else
  {
    $sql = "
      update response_roles 
         set qualifier='$value'
       where user_id='$user_id'
         and year=$year
         and submitted=$submitted
         and item_id=$item_id;";

    error_log($sql);

    $result = $db->query($sql);

    if( ! $result ) { 
      $sql = "
        insert into response_roles
               (user_id, year, submitted, item_id, selected, qualifer)
        values ('$user_id', $year, $submitted, $group_index, 0, '$value'); ";

      error_log($sql);
      $result = $db->query($sql);
    }
  }
  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_update_freetext($db,$year,$user_id,$item_id,$value,$submitted)
{
  error_log('db_update_freetext');

  db_update_participation_history($db,$year,$user_id,$submitted);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->real_escape_string($value);

  if( strlen($value) == 0 )
  {
    $sql = "
      delete from response_free_text
       where user_id='$user_id'
         and year=$year
         and submitted=$submitted
         and item_id=$item_id;";

    error_log($sql);

    $result = $db->query($sql);
  }
  else
  {
    $sql = "
      insert into response_free_text
             (user_id, year, submitted, item_id, text)
      values ('$user_id', $year, $submitted, $item_id, '$value')
          on duplicate key update
             text = '$value';";

    error_log($sql);
    $result = $db->query($sql);
  }

  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_transfer_group_comment($db,$year,$from_id,$to_id,$group_index,$submitted)
{
  error_log('db_transfer_group_comment');

  try
  {
    $sql = "
      select text 
        from response_group_comment
       where user_id = '$from_id'
         and year = $year
         and submitted = $submitted
         and group_index = $group_index; ";

    error_log($sql);

    $result = $db->query($sql);

    $n = $result->num_rows;

    if( $n > 0 )
    {
      $row = $result->fetch_row();
      $text = $row[0];
    }
    else
    {
      $text = '';
    }
    $result->close();
    db_update_group_comment($db,$year,$to_id,$group_index,$text,$submitted);

    $sql = "
      delete from response_group_comment
       where user_id = '$from_id'
         and year = $year
         and submitted = $submitted
         and group_index = $group_index; ";

    error_log($sql);

    $result = $db->query($sql);

  }
  catch(Exception $e)
  {}
}

function db_transfer_freetext($db,$year,$from_id,$to_id,$item_id,$submitted)
{
  error_log('db_transfer_freetext');

  try
  {
    $sql = "
      select text 
        from response_free_text
       where user_id = '$from_id'
         and year = $year
         and submitted = $submitted
         and item_id = $item_id; ";

    error_log($sql);

    $result = $db->query($sql);

    $n = $result->num_rows;

    if( $n > 0 )
    {
      $row = $result->fetch_row();
      $text = $row[0];
    }
    else
    {
      $text = '';
    }
    $result->close();
    db_update_freetext($db,$year,$to_id,$item_id,$text,$submitted);

    $sql = "
      delete from response_free_text
       where user_id = '$from_id'
         and year = $year
         and submitted = $submitted
         and item_id = $item_id; ";

    $result = $db->query($sql);
  }
  catch(Exception $e)
  {}
}
