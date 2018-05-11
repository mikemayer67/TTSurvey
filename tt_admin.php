<?php
// Note that this uses the same css file (ttr.ccs) as the summary page
// While all IDs and variable names use the 'tta' prefix,
//   css classes use the 'ttr' prefix

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/tta_login.php");

$title = preg_replace("/$tt_year/",$tt_active_year,$tt_title);
$tt_year = $tt_active_year;

$statics = db_active_statics();

$user_info = db_all_participants();
usort( $user_info, 'sortInfoByLastName' );

$user_info_map = array();
foreach ($user_info as $info)
{
  $user_info_map[ $info['id'] ] = $info;
}

$submitted             = db_who_has_submitted_forms($tt_year);
$unsubmitted           = db_who_has_unsubmitted_forms($tt_year);

$userids_with_unsubmitted_updates = array();
$userids_with_unsubmitted_surveys = array();
foreach ( array_keys($unsubmitted) as $userid )
{
  if( isset($submitted[$userid]) )
  {
    $userids_with_unsubmitted_updates[] = $userid;
  }
  else
  {
    $userids_with_unsubmitted_surveys[] = $userid;
  }
}

?>

<!DOCTYPE html>
<html>
<head>

<?php require("$dir/tt_head.php"); ?>

<script src='js/tt_admin.js?v=<?=rand()?>'></script>
<link rel='stylesheet' type='text/css' href='ttr.css?v=<?=rand()?>'>

</head>

<body class=ttr>

<div id="tta-fix-email-popup" data-role='popup' class='ui-content tta-popup' data-overlay-theme='b'>
<div>
<label>Email for <span id='tta-fix-email-name'>???</span>:</label>
<input id='tta-fix-email-new' type='text' value="???"/>
<div>
<button class='tta-cancel' data-inline='true'>Cancel</button>
<button class='tta-ok' data-inline='true' disabled='true'>OK</button>
</div>
</div>
</div>


<div id="tta-fix-name-popup" data-role='popup' class='ui-content tta-popup' data-overlay-theme='b'>
<div>
<label>Change name for <span id='tta-fix-name-old'>???</span>:</label>
<input id='tta-fix-name-new' type='text' value="???"/>
<div>
<button class='tta-cancel' data-inline='true'>Cancel</button>
<button class='tta-ok' data-inline='true' disabled='true'>OK</button>
</div>
</div>
</div>


<h1><img src='img/cts_logo.png' height=50><?=$title?> Admin Functions</h1>
<div data-role=collapsibleset>

<div id=tta-userid-block data-role=collapsible>
<h2 id=user-id-help>User IDs</h2>

<div class='ui-field-contain tt-send-id-everyone'>
<?php 
$welcome_sent = $statics['welcome_sent'];
if( is_null($welcome_sent) ) 
{ 
  print "<div class='tta-list-header'>Click";
  print "<button id='send-id-everyone' data-year='$tt_active_year' class='ui-btn-inline ui-btn'>here</button>";
  print "to send User ID reminders to everyone</div>\n";
} 
else 
{
  $welcome_sent = date('l, F j, Y \a\t h:i:s a', $welcome_sent);
  print "<div class='tta-list-header'>User ID reminders were sent out to everyone on: $welcome_sent</div>\n";
}
?>
</div>

<table data-role='table' id='tt-user-info-table' class='ui-responsive'>
  <thead class='tt-bottom-border'>
    <th>Name</th>
    <th>User ID</th>
    <th>Email</th>
    <th><abbr title="Last year participation">Year</abbr></th>
  </thead>
  <tbody>
<?php

$now = time();

foreach ( $user_info as $info )
{
  $id    = $info['id'];
  $name  = $info['name'];
  $email = $info['email'];
  $year  = $info['year'];
  $last_reminder = $info['reminder'];

  $disabled = '';
  if( is_null($email) )
  {
    $disabled = "no email";
  }
  if( ! is_null($last_reminder) ) 
  {
    $next_reminder = $last_reminder + $tta_reminder_frequency;
    if( $now < $next_reminder ) 
    {
      $disabled = "sent " . date('M j \a\t h:i a', $last_reminder);
    }
  }

  print "<tr class='tta-userid tt-bottom-border' data-id='$id' data-name='$name' data-email='$email'>";
  print "<td class='tta-name'><span>$name</span></td>";
  print "<td class='tta-id'><span>$id</span></td>";
  print "<td class='tta-email'><span>$email</span></td>";
  print "<td class='tta-year'><span>$year</span></td>";
  print "<td><div class='tt-admin-user-actions'>";
  print "<button class='ui-btn-inline ui-btn ui-mini tta-send-id'";
  if( strlen($disabled)>0 ) { print " disabled='true'"; }
  print ">send ID</button>";
  print "<button class='ui-btn-inline ui-btn ui-mini tta-fix-name'>fix name</button>";
  print "<button class='ui-btn-inline ui-btn ui-mini tta-fix-email'>fix email</button>";
  print "</div></td>";
  print "<td class='tta-nosend-rationale'>$disabled</td></tr>\n";
}

?>
  </tbody>
</table>

</div>

<div id=tta-submitted-block data-role=collapsible>
<h2 id=submitted-surveys>Submitted Surveys</h2>
<?php
if( count($submitted) == 0 )
{
  print "<div class='tta-empty-list'>No surveys have been submitted yet.</div>\n";
}
else
{
?>
<div class='tta-list-header'>The following people have submitted a <?=$tt_year?> survey.</div>

<div class='tta-list'>
<?php

$has_unsubmitted = false;
foreach ( $user_info as $info )
{
  $id    = $info['id'];

  if(isset($submitted[$id]))
  {
    $name = $info['name'];

    $extra = '';
    if(isset($unsubmitted[$id]))
    {
      $name = "$name<span class='tta-star'>*</span>";
      $has_unsubmitted = true;
    }

    print "<div class='tta-name'>$name</div>\n";
  }
}
print "</div>\n";
if( $has_unsubmitted )
{
  print "<div class='tta-list-footer'>* has made unsubmitted updates</div>\n";
}
}
?>
</div>

<div id=tta-started-block data-role=collapsible>
<h2 id=started-surveys>Unsubmitted Updates</h2>
<?php
if( count($userids_with_unsubmitted_updates) == 0 )
{
  print "<div class='tta-empty-list'>All survey updates have been submitted.</div>\n";
}
else
{
?>
<div class='tta-list-header'>The following people have submitted a <?=$tt_year?> survey, but have made chagnes that have not yet been submitted.</div> <div class='tta-click-here'>Click 
<button id='notify-unsubmitted-updates' data-year='<?=$tt_active_year?>' class='ui-btn-inline ui-btn'>here</button>
to send an email to remind them to participate.</div>

<div class='tta-list'>
<?php

foreach ( $userids_with_unsubmitted_updates as $id )
{
  if( isset( $user_info_map[$id] ) )
  {
    $info = $user_info_map[$id];
    $name = $info['name'];
    print "<div class='tta-name'>$name</div>\n";
  }
}
print "</div>\n";
}
?>
</div>

<div id=tta-started-block data-role=collapsible>
<h2 id=started-surveys>Unsubmitted Surveys</h2>
<?php
if( count($userids_with_unsubmitted_surveys) == 0 )
{
  print "<div class='tta-empty-list'>All started surveys have been submitted</div>\n";
}
else
{
?>
<div class='tta-list-header'>The following people have started a <?=$tt_year?> survey, but have not yet submitted it. </div> <div class='tta-click-here'>Click 
<button id='notify-unsubmitted-surveys' data-year='<?=$tt_active_year?>' class='ui-btn-inline ui-btn'>here</button>
to send an email to let them know</div>

<div class='tta-list'>
<?php

foreach ( $userids_with_unsubmitted_surveys as $id )
{
  if( isset( $user_info_map[$id] ) )
  {
    $info = $user_info_map[$id];
    $name = $info['name'];
    print "<div class='tta-name'>$name</div>\n";
  }
}
print "</div>\n";
}
?>
</div>

<div id=tta-unstarted-block data-role=collapsible>
<h2 id=unstarted-surveys>Unstarted Surveys</h2>
<div class='tta-list-header'>The following people have submitted surveys in the past, but have not yet started one this year.</div> <div class='tta-click-here'>Click 
<button id='send-survey-reminders' data-year='<?=$tt_active_year?>' class='ui-btn-inline ui-btn'>here</button>
to send an email to remind them to participate.</div>

<div class='tta-list'>
<?php

foreach ( $user_info as $info )
{
  $id    = $info['id'];

  if( ! ( isset($unsubmitted[$id]) || isset($submitted[$id]) ))
  {
    $name = $info['name'];
    print "<div class='tta-name'>$name</div>\n";
  }
}
print "</div>\n";
?>
</div>

</div>

</body>
</html>

<?php
function sortInfoByLastName($a,$b)
{
  $aa = explode(' ', $a['name']);
  $bb = explode(' ', $b['name']);

  $aLast = end($aa);
  $bLast = end($bb);

  return strcasecmp($aLast, $bLast);
}
?>
