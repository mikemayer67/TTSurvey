<?php

require_once(dirname(__FILE__).'/db.php');

if ( isset( $_REQUEST['user_id'] ) )
{
  $user_id  = $_REQUEST['user_id'];

  if( $db = db_connect() )
  {
    $sql = "select user_id from participants where user_id='$user_id'";
    
    if( $result = $db->query($sql) )
    {
      $n = $result->num_rows;
      $result->close();

      if( $n == 1 )
      {
        $_SESSION['user_id'] = $user_id;
        setcookie('USER_ID', $_SESSION['user_id'], time()+30*86400);

        require(dirname(__FILE__).'/survey.php');
      }
      else
      {
        error_log(__FILE__ .":: Invalid user_id provided ($user_id)");
        if( $tt_nojs )
        {
          $tt_error = "Invalid User ID ($user_id) entered";
          require(dirname(__FILE__).'/user_id_prompt.php');
        }
        else
        {
          require(dirname(__FILE__).'/500.php');
        }
      }
    }
    else
    {
      error_log(__FILE__.":: Invalid SQL at line ".__LINE__.": $sql");
      require(dirname(__FILE__).'/500.php');
    }
  }
  else
  {
    error_log(__FILE__.":: Failed to connect to database");
    require(dirname(__FILE__).'/500.php');
  }
}
else
{
  error_log(__FILE__.":: Invoked without user_id in \$_REQUEST");
  require(dirname(__FILE__).'/404.php');
}

?>

