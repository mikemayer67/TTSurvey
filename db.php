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
  else
  {
    $tmp_db = false;
  }

  $sql = "select user_id from participants where user_id='$id'";
  $result = db_query($db,$sql);

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
    $result = db_query($db,$sql);

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

function db_add_participation_history($db,$year,$user_id)
{
  $sql = "insert ignore into participation_history values ('$user_id',$year,0)";

  $result = $db->query($sql);
}

function db_survey_groups($db,$year)
{
  $data = array();

  $sql = "select * from survey_groups where year=$year order by group_index";
  $result = db_query($db,$sql);

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

  $result = db_query($db,$sql);

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

  $result = db_query($db,$sql);

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

  $result = db_query($db,$sql);

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

  $result = db_query($db,$sql);

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

function db_clear_unsubmitted($db,$year,$user_id)
{
  db_clear($db,$year,$user_id,0);
}

function db_clear_submitted($db,$year,$user_id)
{
  db_clear($db,$year,$user_id,1);
}

function db_clear($db,$year,$user_id,$submitted)
{
  $tables = array(
    'response_group_comment',
    'response_free_text',
    'response_role_options', 
    'response_roles',
    'participation_history' );

  foreach ( $tables as $table )
  {
    $sql = "
      delete from $table 
       where user_id='$user_id' 
         and year=$year 
         and submitted=$submitted";

    $result = db_query($db,$sql);
  }


}

function db_promote($db,$year,$user_id)
{
  db_clear($db,$year,$user_id,1);

  // note that response_role_options will cascade update
  $tables = array(
    'response_group_comment', 
    'response_free_text', 
    'response_roles',
    'participation_history' );

  foreach ( $tables as $table )
  {
    $sql = "
      update $table 
         set submitted=1
       where user_id='$user_id'
         and year=$year; ";

    $result = db_query($db,$sql);
  }

}

function db_update_role($db,$year,$user_id,$item_id,$value)
{
  db_add_participation_history($db,$year,$user_id);

  $sql = "
    insert into response_roles
           (user_id, year, submitted, item_id, selected)
    values ('$user_id', $year, 0, $item_id, $value)
        on duplicate key update
           selected = $value;";

  $result = db_query($db,$sql);
}

function db_update_role_option($db,$year,$user_id,$item_id,$option_id,$value)
{
  db_add_participation_history($db,$year,$user_id);

  db_update_role($db,$year,$user_id,$item_id,0);   // Danger <--- not sure if this will lead to MySQL integrity issue

  $sql = "
    insert into response_role_options
           (user_id, year, submitted, item_id, option_id, selected)
    values ('$user_id', $year, 0, $item_id, $option_id, $value)
        on duplicate key update
           selected = $value;";

  $result = db_query($db,$sql);
}

function db_update_group_comment($db,$year,$user_id,$group_index,$value)
{
  db_add_participation_history($db,$year,$user_id);

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
         and submitted=0
         and group_index=$group_index;";
  }
  else
  {
    $sql = "
      insert into response_group_comment
            (user_id, year, submitted, group_index, text)
      values ('$user_id', $year, 0, $group_index, '$value')
          on duplicate key update
            text = '$value';";
  }
  $result = db_query($db,$sql);
}

function db_update_role_qualifier($db,$year,$user_id,$item_id,$value)
{
  db_add_participation_history($db,$year,$user_id);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->real_escape_string($value);

  if( strlen($value) == 0 )
  {
    $sql = "
      update response_roles 
         set qualifier = null
       where user_id='$user_id'
         and year=$year
         and submitted=0
         and item_id=$item_id;";

    $result = $db->query($sql);
  }
  else
  {
    $sql = "
      update response_roles 
         set qualifier='$value'
       where user_id='$user_id'
         and year=$year
         and submitted=0
         and item_id=$item_id;";

    $result = $db->query($sql);

    if( ! $result ) { 
      $sql = "
        insert into response_roles
               (user_id, year, submitted, item_id, selected, qualifer)
        values ('$user_id', $year, 0, $group_index, 0, '$value'); ";

      $result = $db->query($sql);
    }
  }
  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
}

function db_update_freetext($db,$year,$user_id,$item_id,$value)
{
  db_add_participation_history($db,$year,$user_id);

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
         and submitted=0
         and item_id=$item_id;";
  }
  else
  {
    $sql = "
      insert into response_free_text
             (user_id, year, submitted, item_id, text)
      values ('$user_id', $year, 0, $item_id, '$value')
          on duplicate key update
             text = '$value';";
  }
  $result = db_query($db,$sql);
}


function db_retrieve_data($db,$year,$user_id)
{
  $data = array();

  if( isset( $_SESSION['ANON_ID'] ) )
  {
    $data = db_retrieve_data_for_user($db,$year,$_SESSION['ANON_ID'],1);

    foreach ($data as $key=>$value)
    {
      $data["anon_$key"] = 1;
    }
  }

  $has_working_copy = db_create_working_copy($db,$year,$user_id);

  if( $has_working_copy )
  {
    $user_data = db_retrieve_data_for_user($db,$year,$user_id,0);
    $data = array_merge($data, $user_data);
    foreach ( $user_data as $key=>$index )
    {
      unset( $data['anon_'.$key] );
    }
  }

  return $data;
}

function db_retrieve_data_for_user($db,$year,$user_id,$submitted)
{
  $data = array();

  $sql = "
    select item_id, text 
      from response_free_text 
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = db_query($db,$sql);

  while( $row = $result->fetch_row() )
  {
    $data["freetext_$row[0]"] = $row[1];
  }

  $sql = "
    select group_index, text 
      from response_group_comment
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = db_query($db,$sql);

  while( $row = $result->fetch_row() )
  {
    $data["comment_$row[0]"] = $row[1];
  }

  $sql = "
    select item_id, selected
      from response_roles
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = db_query($db,$sql);

  while( $row = $result->fetch_row() )
  {
    $data["item_$row[0]"] = (int)($row[1]);
  }

  $sql = "
    select item_id, qualifier
      from response_roles
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = db_query($db,$sql);

  while( $row = $result->fetch_row() )
  {
    if( ! is_null($row[1]) )
    {
      $data["qual_$row[0]"] = $row[1];
    }
  }

  $sql = "
    select item_id, option_id, selected
      from response_role_options
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = db_query($db,$sql);

  while( $row = $result->fetch_row() )
  {
    $data['item_'.$row[0]."_$row[1]"] = (int)($row[2]);
  }

  return $data;
}

function db_create_working_copy($db,$year,$user_id)
{
  $result = db_query($db,"select submitted from participation_history where year=$year and user_id='$user_id'");

  $n = $result->num_rows;
  if($n>1) { return true; }
  if($n<1) { return false; }

  $row = $result->fetch_row();
  if($row[0] == 0) { return true; }

  $tables = array(
    'response_group_comment',
    'response_free_text',
    'response_roles',
    'response_role_options', 
    'participation_history' );

  $columns = array(
    'response_group_comment' => 'user_id, year, 0, group_index, text', 
    'response_free_text'     => 'user_id, year, 0, item_id, text', 
    'response_roles'         => 'user_id, year, 0, item_id, selected, qualifier', 
    'response_role_options'  => 'user_id, year, 0, item_id, option_id, selected', 
    'participation_history'  => 'user_id, year, 0');

  foreach ($tables as $table)
  {
    $col = $columns[$table];
    $sql = "
      insert into $table 
      select $col
        from $table 
       where year=$year 
         and user_id='$user_id'
         and submitted=1 ;";

    db_query($db,$sql);
  }

  return true;
}

function db_can_revert($db,$year,$user_id)
{
  $result = db_query($db,"select submitted from participation_history where year=$year and user_id='$user_id' and submitted=1");

  $n = $result->num_rows;
  return $n>0;
}


function db_query($db,$sql)
{
  $result = $db->query($sql);
  if( ! $result ) { throw new Exception("Invalid SQL: $sql",500); }
  return $result;
}

function db_all_results($db,$year)
{
  $result = db_query($db,"select group_index, label from survey_groups where year=$year");

  // structure

  $groups = array();
  while($row = $result->fetch_row())
  {
    list($group_index,$label) = $row;
    $groups[$group_index] = array('label'=>$label);
  }
  $result->close();

  $result = db_query($db, "
    select   item_id, group_index, coalesce(summary_label,label) 
    from     survey_items
    where    year=$year
    and      item_type='role'
    order by group_index,order_index; ");
  
  $roles = array();
  while($row = $result->fetch_row())
  {
    list($item_id,$group_index,$label) = $row;
    $groups[$group_index]['roles'][] = $item_id;
    $roles[$item_id] = array( 'label'=>$label );
  }
  $result->close();

  $result = db_query($db,"
    select   a.item_id, a.option_id, a.option_label
    from     survey_role_options a, survey_items b
    where    a.item_id = b.item_id
    and      b.year=$year" );

  while($row = $result->fetch_row())
  {
    list($item_id,$option_id,$label) = $row;
    $roles[$item_id]['options'][$option_id] = $label;
  }

  $result = db_query($db,"
    select   item_id, group_index, coalesce(summary_label,label) 
    from     survey_items
    where    year=$year
    and      item_type='free_text'
    order by group_index,order_index; ");
  
  $free_text = array();
  while($row = $result->fetch_row())
  {
    list($item_id,$group_index,$label) = $row;
    $groups[$group_index]['free_text'][] = $item_id;
    $free_text[$item_id] = $label;
  }
  $result->close();

  // responses
  
  $result = db_query($db,"
  select    a.name,
            b.item_id,
            b.selected,
            c.option_id,
            b.qualifier
  from      participants a, 
            response_roles b 
  left join response_role_options c 
  on        (c.item_id=b.item_id and c.user_id=b.user_id and c.year=b.year)
  where     b.year=$year
  and       a.user_id=b.user_id
  and       b.submitted=1
  and       ( c.selected = 1 or c.selected is null ); " );

  $response_summary = array();
  $user_responses = array();

  while($row = $result->fetch_row())
  {
    list($name,$item_id,$selected,$option_id,$qualifier) = $row;

    if(is_null($option_id)) 
    {
      if($selected) 
      {
        $response_summary[$item_id][$name]['selected'] = 1;
        $user_responses[$name]['roles'][$item_id]['selected'] = 1;
        if( ! is_null($qualifier) ) 
        {
          $response_summary[$item_id][$name]['qualifier'] = $qualifier;
          $user_responses[$name]['roles'][$item_id]['qualifier'] = $qualifier;
        }
      }
    }
    else 
    {
      $response_summary[$item_id][$name]['options'][$option_id] = 1;
      $user_responses[$name]['roles'][$item_id]['options'][$option_id] = 1;
      if( ! is_null($qualifier) )
      {
        $response_summary[$item_id][$name]['qualifier'] = $qualifier;
        $user_responses[$name]['roles'][$item_id]['qualifier'] = $qualifier;
      }
    }
  }
  $result->close();

  $result = db_query($db,"
  select    a.name,
            b.group_index,
            b.text
  from      participants a, 
            response_group_comment b 
  where     b.year=$year
  and       a.user_id=b.user_id
  and       b.submitted=1
  and       b.text is not null;" );

  $comment_summary = array();
  $user_comments = array();
  while($row = $result->fetch_row())
  {
    list($name,$group_index,$text) = $row;
    $comment_summary[$group_index][$name] = $text;
    $user_responses[$name]['comments'][$group_index] = $text;
  }
  $result->close();


  $result = db_query($db,"
  select    a.name,
            b.item_id,
            b.text
  from      participants a, 
            response_free_text b 
  where     b.year=$year
  and       a.user_id=b.user_id
  and       b.submitted=1
  and       b.text is not null;" );

  while($row = $result->fetch_row())
  {
    list($name,$item_id,$text) = $row;
    $response_summary[$item_id][$name] = $text;
    $user_responses[$name]['free_text'][$item_id] = $text;
  }
  $result->close();

  $result = db_query($db,"
  select    b.item_id,
            b.text
  from      response_free_text b 
  where     b.year=$year
  and       not exists ( select * from participants a where a.user_id=b.user_id )
  and       b.submitted=1
  and       b.text is not null;" );

  while($row = $result->fetch_row())
  {
    list($item_id,$text) = $row;
    $anonymous_summary[$item_id][] = $text;
  }
  $result->close();

  $data = array( 'groups'            => $groups,
                 'roles'             => $roles,
                 'free_text'         => $free_text,
                 'response_summary'  => $response_summary,
                 'comment_summary'   => $comment_summary,
                 'anonymous_summary' => $anonymous_summary,
                 'user_responses'    => $user_responses,
               );

  return $data;
}
