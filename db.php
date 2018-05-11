<?php

class TTDB {
  public  $db = null;

  const DB_USER = 'ctsluthe_ttadm';
  const DB_PASS = 'ctsluthe_ttadm';
  const DB_NAME = 'ctsluthe_ttsurvey';
  const DB_HOST = 'localhost';

  function __construct($db=null)
  {
    if(is_null($this->db))
    {
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

      $err = $mysqli->connect_error;
      if( $err )
      {
        $errno = $mysqli->connect_errno;
        throw new Exception( "Failed to connect to database[$errno]: $err", 500 );
      }

      if( ! $mysqli->set_charset('utf8') ) 
      { 
        throw new Exception('Failed to set charset to utf8',500); 
      }

      $this->db = $mysqli;
    }
  }

  public function query($sql)
  {
    $result = $this->db->query($sql);
    if( ! $result ) 
    { 
      $sql = preg_replace('/\s+/',' ',$sql);
      $sql = preg_replace('/^\s/','',$sql);
      $sql = preg_replace('/\s$/','',$sql);

      $trace = debug_backtrace();
      $file = $trace[0]["file"];
      $line = $trace[0]["line"];

      throw new Exception("Invalid SQL: $sql  [invoked at: $file:$line]",500); 
    }
    return $result;
  }

  public function escape($value)
  {
    return $this->db->real_escape_string($value);
  }

//
// STATICS
//

// Get the current (active) statics
function db_active_statics()
{
  $db = new TTDB;

  $result = $db->query("select * from statics where active=1");
  $n = $result->num_rows;

  if($n>1) { throw new Exception('Too many active entries in the statics table',500); }
  if($n<1) { throw new Exception('No active entries in the statics table',500); }

  $data = $result->fetch_assoc();
  return $data;
}

// Get the statics for the specified year
function db_get_statics($year)
{
  $db = new TTDB;

  $result = $db->query("select * from statics where year=$year");
  $n = $result->num_rows;

  if($n<1) { throw new Exception("No entries in the statics table for $year",500); }

  $data = $result->fetch_assoc();
  return $data;
}

// Update the current (active) value for the specified static
function db_update_statics($key,$value)
{
  $db = new TTDB;

  if( is_string($value) )
  {
    $db->query("update statics set $key='$value' where active=1");
  }
  else
  {
    $db->query("update statics set $key=$value where active=1");
  }
}

//
// USER IDS
//

// Generates and records a new userid.
function db_gen_user_id()
{
  $db = new TTDB;

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

    $result = $db->query("select user_id from user_ids where user_id='$id'");

    $n = $result->num_rows;
    $result->close();

    if( $n==0 )
    {
      $db->query("insert into user_ids values('$id')");
      return $id;
    }
  }

  throw new Exception("Failed to generate a unique ID in $max_attempts attempts", 500);
}

//
// USER INFO
//

// Sets the last reminder date to current time
function db_update_reminder($userid,$idb=null)
{
  $db = new TTDB;

  $now = time();

  $db->query("update participants set reminder=$now where user_id='$userid'");
}

// Verifies that specified user id is a valid particpant id
function db_verify_userid($id)
{
  $db = new TTDB;
  $id = strtoupper($id);

  $result = $db->query("select user_id from participants where user_id='$id'");

  $n = $result->num_rows;
  $result->close();

  return $n==1;
}

// Returns all information about the specified uesrid
function db_get_user_info($id)
{
  $db = new TTDB;
  $id = strtoupper($id);

  $data = array();
  try
  {
    $sql = "select name,email,reminder from participants where user_id='$id'";
    $result = $db->query($sql);

    $n = $result->num_rows;
    if( $n == 1 ) 
    { 
      $data = $result->fetch_assoc(); 
    }
    $result->close();
  }
  catch(Exception $e)
  {
    error_log($e->getMessage());
  }

  return $data;
}

// Updates the name information for the specified userid
function db_update_user_name($id,$name)
{
  $db = new TTDB;
  $id = strtoupper($id);

  $rval = false;
  try
  {
    $name = trim($name);

    $sql = "update participants set name='$name',reminder=NULL where user_id='$id'";
    $rval = $db->query($sql);
  }
  catch(Exception $e)
  {
    error_log($e->getMessage());
  }

  return $rval;
}

// Updates the email information for the specified userid
function db_update_user_email($id,$email)
{
  $db = new TTDB;
  $id = strtoupper($id);

  $rval = false;
  try
  {
    $email = trim($email);

    $sql = "update participants set email='$email',reminder=NULL where user_id='$id'";
    $rval = $db->query($sql);
  }
  catch(Exception $e)
  {
    error_log($e->getMessage());
  }

  return $rval;
}


//
// PATICIPATION
//

// Remove a user's participation history for specified year.
//   This will cascade to remove all responses for that user/year
public function db_drop_participation($year,$user_id,$submitted)
{
  $db = new TTDB;

  $sql = <<<SQL
delete from participation_history 
where  user_id='$user_id' and
  and  year=$year  and
  and  submitted=$submitted
SQL;

  $result = $db->query($sql);
}

// Adds a new participant to both the particpants and participation_history
// tables.  If userid already exists, an exception will be thrown
function db_add_new_participant($id,$name,$email,$year)
{
  $db = new TTDB;
  $id = strtoupper($id);

  $db->query("insert into participants values ('$id','$name','$email',NULL)");
  $db->query("insert into participation_history values ('$id',$year,0)");
}

// Updates the participation status for the specified userid
// The status is set to indicate that a survey was started
function db_record_survey_start($year,$user_id)
{
  $db = new TTDB;
  $sql = "insert ignore into participation_history values ('$user_id',$year,0)";
  $result = $db->query($sql);
}


// Gets info + last participation year for all users
function db_all_participants()
{
  $db = new TTDB;

  $rval = array();

  $sql = <<<SQL
select user_id, max(year)
from   participation_history
where  submitted = 1
group  by user_id
SQL;

  $result = $db->query($sql);

  $ids = array();
  while( $row = $result->fetch_row() )
  {
    list($id,$year) = $row;
    $ids[$id] = $year;
  }

  foreach($ids as $id=>$year)
  {
    $info = db_get_user_info($id);
    $info['id']=$id;
    $info['year']=$year;
    $rval[] = $info;
  }

  return $rval;
}

//
// SURVEY CONTENT
//

function db_survey_groups($year)
{
  $db = new TTDB;

  $data = array();

  $sql = "select * from survey_groups where year=$year order by group_index";
  $result = $db->query($sql);

  while( $row = $result->fetch_assoc() )
  {
    $data[] = $row;
  }

  $result->close();

  return $data;
}

//---- WORK HERE ----


function db_submitted_in_year($idb,$year)
{
  return db_participation_in_year($idb,$year,1);
}

function db_unsubmitted_in_year($idb,$year)
{
  return db_participation_in_year($idb,$year,0);
}

function db_participation_in_year($idb,$year,$submitted=NULL)
{
  $db = new LocalDB($idb);

  $data = array();

  $sql = "
    select 
      p.user_id id, 
      p.name  name
    from 
      participants p,
      participation_history h
    where
      p.user_id = h.user_id and
      h.year = $year";
  if( ! is_null($submitted)) { $sql = "$sql and h.submitted = $submitted"; }
  $sql = "$sql;";

  $result = $db->query($sql);

  while( $row = $result->fetch_assoc() )
  {
    $data[$row['id']] = $row['name'];
  }

  $result->close();

  return $data;
}

function db_survey_items($idb,$year,$group)
{
  $db = new LocalDB($idb);

  $data = array();

  $sql = "
    select 
      s.item_id,
      s.order_index,
      i.item_type,
      i.anonymous,
      l.type,
      l.level,
      l.italic,
      l.bold,
      l.size,
      coalesce(l.value,i.label) label
    from 
      survey s,
      survey_items i left join survey_labels l on (l.item_id=i.item_id)
    where
      s.year = $year and
      s.group_index = $group and 
      i.item_id=s.item_id; ";

  $result = $db->query($sql);

  while( $row = $result->fetch_assoc() )
  {
    $data[] = $row;
  }

  $result->close();

  return $data;
}

function db_role_options($idb,$year)
{
  $db = new LocalDB($idb);

  $rval = array();

  $sql = "
    select a.item_id,
           a.option_id,
           a.option_label,
           a.is_primary
      from survey s, survey_role_options a
     where s.year = $year
       and a.item_id = s.item_id
     order by item_id,option_id;";

  $result = $db->query($sql);

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

function db_role_qualifiers($idb,$year)
{
  $db = new LocalDB($idb);

  $rval = array();

  $sql = "
    select concat(a.item_id,'_',a.qualification_option),
           a.qualification_hint
      from survey s, survey_role_qualifiers a
     where s.year = $year
       and a.item_id = s.item_id;";

  $result = $db->query($sql);

  while( $row = $result->fetch_row() )
  {
    list($id,$hint) = $row;
    $rval[$id] = $hint;
  }
  $result->close();

  return $rval;
}

function db_role_dependencies($idb,$year)
{
  $db = new LocalDB($idb);

  $rval = array();

  $sql = "
    select concat(a.item_id,'_',a.option_id) as child,
           concat(a.item_id,'_',a.require_option_id) as parent 
      from survey s, survey_role_options a
     where a.is_primary=0
       and s.year = $year
       and a.item_id = s.item_id;";

  $result = $db->query($sql);

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

function db_clear_unsubmitted($idb,$year,$user_id)
{
  $db = new LocalDB($idb);
  $db->db_drop_participation($year,$user_id,0);
}

function db_clear_submitted($idb,$year,$user_id)
{
  $db = new LocalDB($idb);
  $db->db_drop_participation($year,$user_id,1);
}

function db_promote($idb,$year,$user_id)
{
  $db = new LocalDB($idb);
  db_clear_submitted($db,$year,$user_id);

  // following will cascade to all response_xxx tables
  $sql = "
    update participation_history
       set submitted=1
     where user_id='$user_id'
       and year=$year";

  $result = $db->query($sql);
}

function db_update_role($idb,$year,$user_id,$item_id,$value)
{
  $db = new LocalDB($idb);

  db_record_survey_start($year,$user_id);

  $sql = "
    insert into response_roles
           (user_id, year, submitted, item_id, selected)
    values ('$user_id', $year, 0, $item_id, $value)
        on duplicate key update
           selected = $value;";

  $result = $db->query($sql);
}

function db_update_role_option($idb,$year,$user_id,$item_id,$option_id,$value)
{
  $db = new LocalDB($idb);

  db_record_survey_start($year,$user_id);

  db_update_role($db,$year,$user_id,$item_id,0);

  $sql = "
    insert into response_role_options
           (user_id, year, submitted, item_id, option_id, selected)
    values ('$user_id', $year, 0, $item_id, $option_id, $value)
        on duplicate key update
           selected = $value;";

  $result = $db->query($sql);
}

function db_update_group_comment($idb,$year,$user_id,$group_index,$value)
{
  $db = new LocalDB($idb);

  db_record_survey_start($year,$user_id);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->escape($value);

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
  $result = $db->query($sql);
}

function db_update_role_qualifier($idb,$year,$user_id,$item_id,$value)
{
  $db = new LocalDB($idb);

  db_record_survey_start($year,$user_id);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->escape($value);

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
}

function db_update_freetext($idb,$year,$user_id,$item_id,$value)
{
  $db = new LocalDB($idb);

  db_record_survey_start($year,$user_id);

  $value = preg_replace('/^\s+/','',$value);
  $value = preg_replace('/\s+$/','',$value);
  $value = preg_replace('/\s+/',' ',$value);
  $value = $db->escape($value);

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
          on duplicate key update text = '$value';";
  }
  $result = $db->query($sql);
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

function db_retrieve_data_for_user($idb,$year,$user_id,$submitted)
{
  $db = new LocalDB($idb);

  $data = array();

  $sql = "
    select item_id, text 
      from response_free_text 
     where user_id='$user_id'
       and year=$year
       and submitted=$submitted";

  $result = $db->query($sql);

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

  $result = $db->query($sql);

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

  $result = $db->query($sql);

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

  $result = $db->query($sql);

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

  $result = $db->query($sql);

  while( $row = $result->fetch_row() )
  {
    $data['item_'.$row[0]."_$row[1]"] = (int)($row[2]);
  }

  return $data;
}

function db_create_working_copy($idb,$year,$user_id)
{
  $db = new LocalDB($idb);

  $result = $db->query("select submitted from participation_history where year=$year and user_id='$user_id'");

  $n = $result->num_rows;
  if($n>1) { return true; }
  if($n<1) { return false; }

  $row = $result->fetch_row();
  if($row[0] == 0) { return true; }

  $tables = array(
    'participation_history',
    'response_group_comment',
    'response_free_text',
    'response_roles',
    'response_role_options'); 

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

    $db->query($sql);
  }

  return true;
}

function db_can_revert($idb,$year,$user_id)
{
  $db = new LocalDB($idb);

  $result = $db->query("select submitted from participation_history where year=$year and user_id='$user_id' and submitted=1");

  $n = $result->num_rows;
  return $n>0;
}

function db_clone_prior_year($idb,$year,$user_id)
{
  $db = new LocalDB($idb);

  // check if already have data for this user this year.  if so, we're done

  $result = $db->query("select submitted from participation_history where year=$year and user_id='$user_id'");

  $n = $result->num_rows;
  if($n>0) { return; } 

  // check if have SUBMITTED data for this user from a prior year.  If not, we're also done

  $result = $db->query("select max(year) from participation_history where user_id='$user_id' and submitted=1 and year<$year");

  $n = $result->num_rows;
  if($n<1) { return; }

  // if we got here, results should contain a single row containing the last year user submitted a form

  $row = $result->fetch_row();
  $ref_year = $row[0];

  error_log("INFO: Cloning $user_id data from $ref_year");

  // start updating the data

  db_record_survey_start($year,$user_id);

  // get survey revision cross references

  $group_xref = array();
  $result = $db->query( "select group_index,prior_year_index from survey_groups where year=$year");
  while( $row = $result->fetch_row() )
  {
    list($new_index,$old_index) = $row;
    $group_xref[$old_index] = $new_index;
  }

  $option_xref = array();
  $result = $db->query( "select new_item_id, new_option_id, old_item_id, old_option_id from revised_option_xref");
  while( $row = $result->fetch_row() )
  {
    list($new_id,$new_opt_id,$old_id,$old_opt_id) = $row;
    $option_xref[$old_id][$old_opt_id][] = array($new_id,$new_opt_id);
  }

  $cur_items = array();
  $result = $db->query("select item_id from survey s where year=$year");
  while( $row = $result->fetch_row() )
  {
    list($id) = $row;
    $cur_items[$id] = 1;
  }
   
  $cur_options = array();
  $sql = "select a.item_id, a.option_id from survey s, survey_role_options a where s.year=$year and a.item_id=s.item_id";
  $result = $db->query($sql);
  while( $row = $result->fetch_row() )
  {
    list($id,$opt_id) = $row;
    $cur_options[$id][$opt_id] = 1;
  }
   
  // copy free text responses
  // TODO: add item xref (see option_xref above and role option responses below )

  $sql = "
    select item_id, text
      from response_free_text
     where year=$ref_year
       and user_id='$user_id'
       and submitted=1 ;";

  $result = $db->query($sql);
  while($row = $result->fetch_row())
  {
    list($item_id,$text) = $row;
    if(isset($cur_items[$item_id]))
    {
      db_update_freetext($db,$year,$user_id,$item_id,$text);
    }
  }

  // copy role responses
  // TODO: add item xref (see option_xref above and role option responses below )

  $sql = "
    select item_id, selected, qualifier
      from response_roles
     where year=$ref_year
       and user_id='$user_id'
       and submitted=1 ;";

  $result = $db->query($sql);
  while($row = $result->fetch_row())
  {
    list($item_id,$selected,$qualifier) = $row;
    if(isset($cur_items[$item_id]))
    {
      db_update_role($db,$year,$user_id,$item_id,$selected);
      db_update_role_qualifier($db,$year,$user_id,$item_id,$qualifier);
    }
  }

  // copy role option responses
  
  $sql = "
    select item_id, option_id, selected
      from response_role_options
     where year=$ref_year
       and user_id='$user_id'
       and submitted=1 ;";

  $result = $db->query($sql);
  while($row = $result->fetch_row())
  {
    list($item_id,$option_id,$selected) = $row;
    if( isset($option_xref[$item_id][$option_id]) )
    {
      $xrefs = $option_xref[$item_id][$option_id];
      foreach( $xrefs as $xref )
      {
        list($new_item_id,$new_option_id) = $xref;
        if (isset($cur_options[$new_item_id][$new_option_id]))
        {
          db_update_role_option($db,$year,$user_id,$new_item_id,$new_option_id,$selected);
          error_log("Cloning option [$user_id,$year,$new_item_id,$new_option_id] from [$ref_year,$item_id,$option_id]");
        }
      }
    }
    else if(isset($cur_options[$item_id][$option_id]))
    {
      db_update_role_option($db,$year,$user_id,$item_id,$option_id,$selected);
      error_log("Cloning option [$user_id,$year,$item_id,$option_id] from $ref_year");
    }
  }

  // copy group level responses

  $last_year = $year - 1;
  $sql = "
    select group_index, text
      from response_group_comment
     where year=$last_year
       and user_id='$user_id'
       and submitted=1 ;";
  $result = $db->query($sql);
  while($row = $result->fetch_row())
  {
    list($old_index,$text) = $row;
    if(isset($group_xref[$old_index]))
    {
      $new_index = $group_xref[$old_index];
      db_update_group_comment($db,$year,$user_id,$new_index,$text);
      error_log("Cloning group comment [$user_id,$year,$new_index] from [$last_year,$old_index]");
    }
  }

}


function db_all_results($idb,$year)
{
  $db = new LocalDB($idb);

  $result = $db->query("select group_index, label from survey_groups where year=$year");

  // structure

  $groups = array();
  while($row = $result->fetch_row())
  {
    list($group_index,$label) = $row;
    $groups[$group_index] = array('label'=>$label);
  }
  $result->close();

  $group_xref = array();

  $result = $db->query("
    select   s.item_id, s.group_index, coalesce(i.summary_label,i.label) 
    from     survey s, survey_items i
    where    year=$year
    and      i.item_id=s.item_id
    and      i.item_type='role'
    order by s.group_index,s.order_index; ");

  $roles = array();
  while($row = $result->fetch_row())
  {
    list($item_id,$group_index,$label) = $row;
    $groups[$group_index]['roles'][] = $item_id;
    $roles[$item_id] = array( 'label'=>$label );
    $group_xref[$item_id] = $group_index;
  }
  $result->close();

  $result = $db->query("
    select   a.item_id, a.option_id, a.option_label
    from     survey s, survey_role_options a
    where    a.item_id = s.item_id
      and    s.year=$year" );

  while($row = $result->fetch_row())
  {
    list($item_id,$option_id,$label) = $row;
    $roles[$item_id]['options'][$option_id] = $label;
  }

  $result = $db->query("
    select   s.item_id, s.group_index, coalesce(i.summary_label,i.label) 
    from     survey s, survey_items i
    where    s.year=$year
    and      i.item_id=s.item_id
    and      item_type='free_text'
    order by group_index,order_index; ");

  $free_text = array();
  while($row = $result->fetch_row())
  {
    list($item_id,$group_index,$label) = $row;
    $groups[$group_index]['free_text'][] = $item_id;
    $free_text[$item_id] = $label;
    $group_xref[$item_id] = $group_index;
  }
  $result->close();

  // responses

  $result = $db->query("
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
    and       b.submitted=1
    and       a.user_id=b.user_id
    and       ( c.selected = 1 or c.selected is null ); " );

  $response_summary = array();
  $user_responses = array();

  while($row = $result->fetch_row())
  {
    list($name,$item_id,$selected,$option_id,$qualifier) = $row;
    $group_index = $group_xref[$item_id];

    if(is_null($option_id)) 
    {
      if($selected) 
      {
        $response_summary[$item_id][$name]['selected'] = 1;
        $user_responses[$name][$group_index]['roles'][$item_id]['selected'] = 1;
        if( ! is_null($qualifier) ) 
        {
          $response_summary[$item_id][$name]['qualifier'] = $qualifier;
          $user_responses[$name][$group_index]['qualifiers'][$item_id] = $qualifier;
        }
      }
    }
    else 
    {
      $response_summary[$item_id][$name]['options'][$option_id] = 1;
      $user_responses[$name][$group_index]['roles'][$item_id]['options'][$option_id] = 1;
      if( ! is_null($qualifier) )
      {
        $response_summary[$item_id][$name]['qualifier'] = $qualifier;
        $user_responses[$name][$group_index]['qualifiers'][$item_id] = $qualifier;
      }
    }
  }
  $result->close();

  $result = $db->query("
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
    $user_responses[$name][$group_index]['comment'] = $text;
  }
  $result->close();


  $result = $db->query("
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
    $group_index = $group_xref[$item_id];
    $response_summary[$item_id][$name] = $text;
    $user_responses[$name][$group_index]['free_text'][$item_id] = $text;
  }
  $result->close();

  $result = $db->query("
    select    b.item_id,
              b.text
    from      response_free_text b 
    where     b.year=$year
    and       not exists ( select * from participants a where a.user_id=b.user_id )
    and       b.submitted=1
    and       b.text is not null;" );

  $anonymous_summary = array();
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
