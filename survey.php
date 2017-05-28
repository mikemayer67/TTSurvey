<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); 

require_once("$dir/db.php");
$user_uid = $_SESSION['USER_ID'];
$user_info = db_user_info($user_uid);
$user_name = $user_info['name'];
$user_email = $user_info['email'];
?>

<script src="js/survey.js?v=<?=rand()?>"></script>

</head>

<body>

<div id=tt_survey_header class=tt-header>
<span id=tt_user_uid class=tt-user-info>User ID: <span><?=$user_uid?></span>
  <button data-role='none'>logout</button></span>
<span id=tt_user_name class=tt-user-info>Name: <span><?=$user_name?></span>
  <input data-role='none' placeholder='Name...' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
<span id=tt_user_email class=tt-user-info>Email: <span><?=$user_email?></span>
  <input data-role='none' placeholder='(optional)' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
</div>

<?=$tt_page_title?>

<form class=tt_survey_form>

<?php

try
{
  $db = db_connect();

  $groups = db_survey_groups($db,$tt_year);

  foreach ( $groups as $group )
  {
    $group_id = $group['group_index'];
    $group_label = $group['label'];
    $collapsible = ( $group['collapsible'] 
      ? "data-role='collapsible' data-collapsed='false' class=tt-collapsible" 
      : 'class=tt-non-collapsible' );

    print "  <div $collapsible>\n";
    print "    <h2 id='survey_group_$group_id' style=tt-group-label>$group_label</h2>\n";

    $items = db_survey_items($db,$tt_year, $group_id);

    foreach ( $items as $item )
    {
      switch( $item['item_type'] )
      {
      case 'label':
        $label = $item['label'];
        error_log($label);

        print "    <p>$label</p>\n";

        break;
      }
    }

    print "  </div>\n";
  }
}
catch (Exception $e)
{
  $msg  = $e->getMessage();
  $file = $e->getFile();
  $line = $e->getLine();

  error_log("$file\[$line\]: $msg");

  $page = $e->getCode() . '.php';
?>
  <script>window.location='<?=$page?>';</script>
<?php
}
finally
{
  $db->close();
}

?>

</form>

</body>
</html>

