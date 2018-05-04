<?php
// Note that this uses the same css file (ttr.ccs) as the summary page
// While all IDs and variable names use the 'tta' prefix,
//   css classes use the 'ttr' prefix

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/tta_login.php");

$title = preg_replace("/$tt_year/",$tt_active_year,$tt_title);
$tt_year = $tt_active_year;

$db = db_connect();

$statics = db_active_survey_statics($db);

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
  print "Click";
  print "<button id='send-id-everyone' data-year='$tt_active_year' class='ui-btn-inline ui-btn'>here</button>";
  print "to send User ID reminders to everyone\n";
} 
else 
{
  $welcome_sent = date('l, F j, Y \a\t h:i:s a', $welcome_sent);
  print "<span>User ID reminders were sent out to everyone on: $welcome_sent</span>\n";
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

$user_info = db_userid_admin($db);
usort( $user_info, 'idsByLastNameSort' );

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
  print "<td class='tta-name'>$name</td>";
  print "<td class='tta-id'>$id</td>";
  print "<td class='tta-email'><span>$email</span></td>";
  print "<td class='tta-year'>$year</td>";
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
The following people have submitted a <?=$tt_year?> survey.
</div>

<div id=tta-started-block data-role=collapsible>
<h2 id=started-surveys>Started Surveys (not yet submitted)</h2>
The following people have started a <?=$tt_year?> survey, but have not yet submitted it.
</div>

<div id=tta-unstarted-block data-role=collapsible>
<h2 id=unstarted-surveys>Unstarted Surveys</h2>
The following people have submitted surveys in the past, but have not yet started one this year.
</div>

</div>

</body>
</html>

<?php
function idsByLastNameSort($a,$b)
{
  $aa = explode(' ', $a['name']);
  $bb = explode(' ', $b['name']);

  $aLast = end($aa);
  $bLast = end($bb);

  return strcasecmp($aLast, $bLast);
}
?>
