<?php

require_once(dirname(__FILE__).'/db.php');

$db = db_connect();

db_drop_unsubmitted($tt_year,$user_id);

if( isset($_SESSION['ANON_ID']) )
{
  $anon_id = $_SESSION['ANON_ID'];
  db_drop_unsubmitted($tt_year,$anon_id);
}

foreach ( $_POST as $key => $value )
{
  $keys  = explode('_', $key);
  $nkeys = count($keys);

  $anon_key = "anon_$key";
  if( isset($_POST[$anon_key]) )
  {
    if( ! isset($anon_id) ) { 
      $anon_id = db_gen_user_id();
      $_SESSION['ANON_ID'] = $anon_id;
    }
    $id = $anon_id;
  }
  else
  {
    $id = $user_id;
  }

  switch($keys[0])
  {
  case 'item':
    switch($nkeys)
    {
    case 2:
      db_update_role($tt_year,$user_id,$keys[1], 1);
      break;
    case 3:
      db_update_role_option($tt_year,$user_id,$keys[1], $keys[2], 1);
      break;

    default:
      throw new Exception('improper set of keys provided for item',500);
    }
    break;

  case 'comment':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for comment',500);
    }
    db_update_group_comment($tt_year,$id,$keys[1], $value);
    break;

  case 'qual':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for qual',500);
    }
    db_update_role_qualifier($tt_year,$user_id,$keys[1], $value);
    break;

  case 'freetext':
    if( $nkeys != 2 ) {
      throw new Exception('improper set of keys provided for freetext',500);
    }
    db_update_freetext($tt_year,$id,$keys[1], $value);
    break;
  }
}

db_submit_responses($tt_year,$user_id);

if( isset($anon_id) ) {
  db_submit_responses($tt_year,$anon_id); 
}

?>

<!DOCTYPE html>
<html>
<head>

<?php require("$dir/tt_head.php"); 

$user_uid = $_SESSION['USER_ID'];
$user_info  = db_get_user_info($user_uid);
$user_name  = $user_info['name'];
$user_email = $user_info['email'];

$link1  = "$tt_root_url/tt.php?uid=$user_uid";
$ctslnk = "$tt_root_url/img/cts_logo.png";

if(isset($_SESSION['ANON_ID']))
{
  $link2 = $link1.'&aid='.$_SESSION['ANON_ID'];
}
?>

</head>

<body>

<?=$tt_page_title?>

<div class=tt-body-div>
<h2>Thank You!</h2>

<p><strong>Your <?=$tt_year?> survey results have been received and recorded.</strong></p>

<p>If you would like to revisit or revise your answers, you can come back at any time using the following link:<p>
<a class=tt-indent href='<?=$link1?>'><?=$link1?></a>

<?php if(isset($link2)) {?>
<p>If you wish to review your anonymous responses as well, you must use the following link:</p>
<div class=tt-nospace>
<a class=tt-indent href='<?=$link2?>'><?=$link2?></a>
<p class="tt-indent tt-note">Note that this is the <strong>only</strong> way to associate you with your anonymous responses</p>
</div>

<div class=tt-nospace>
<p>Alternatively, you can simply return to the <?=$tt_title?> website and enter your User ID: <strong><?=$user_uid?></strong>.

<p class=tt-indent>If you lose <?php if(isset($link2)) { print "these links"; } else { print "this link"; } ?>, 
you can contact <?=$tt_poc?> at <?=$tt_poc_email?> to look up your user ID.</p> 
<?php if(isset($link2)) { ?> 
<?php } ?>
</div>

<?php
}
if(isset($user_email)) { ?>
  <p>A copy of this information has also been sent to the email address you provided: <strong><?=$user_email?></strong></p>
<?php

  if( ! isset($link2) ) { $link2 = null; }
  email_submission_feedback($user_uid,$user_email, $tt_title, $tt_poc, $tt_poc_email, $ctslnk, $link1, $link2);
} ?>

</div>
</body>

