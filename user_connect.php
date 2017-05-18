<?php

header('Location: 500.php');

session_start();

$tt_nojs = isset($_REQUEST['nojs']);
error_log("nojs: $tt_nojs");

if( isset( $_POST['user_name'] ) && isset( $_POST['user_email'] ) )
{
  $name  = $_POST['user_name'];
  $email = $_POST['user_email'];
}
else if ( isset( $_POST['user_id'] ) )
{
  $user_id  = $_POST['user_id'];

  if( $db = db_connect() )
  {
    $sql = "select $user_id from from participants where user_id='$user_id'";
    
    my $result = $db->query($sql);
    my $n      = $result->num_rows;

    $result->close();
    $db->close();

    if( $n == 2 )
    {
      $_SESSION['user_id'] = $user_id;
      setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400);
    }
    else
    {
      error_log(__FILE__ .":: Invalid user_id provided (" . __LINE__ ."): $sql");
      if( $tt_nojs )
      {
        $_TT_ERROR = 'Invalid User ID';
        require('tt.php');
        return;
      }
    }
    $db->close();
  }
}

header('Location: 500.php');

?>

