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

<h1><img src='img/cts_logo.png' height=50><?=$title?> Admin Functions</h1>
<div data-role=collapsibleset>

<div id=tta-userid-block data-role=collapsible>
<h2 id=user-id-help>User IDs</h2>

<div class='ui-field-contain tt-send-id-everyone'>
<?php 
$userids_sent = $statics['userids_sent'];
if( is_null($userids_sent) ) 
{ 
  print "Click";
  print "<button id='send-id-everyone' data-year='$tt_active_year' class='ui-btn-inline ui-btn'>here</button>";
  print "to send User ID reminders to everyone\n";
} 
else 
{
  $userids_sent = date('l, F j, Y \a\t h:i:s a', strtotime($userids_sent));
  print "<span>User ID reminders were sent out to everyone on: $userids_sent</span>\n";
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

foreach ( $user_info as $info )
{
  print "<tr class='tt-bottom-border'><td>";
  print $info['name'];
  print "</td><td>";
  print $info['id'];
  print "</td><td>";
  print $info['email'];
  print "</td><td>";
  print $info['year'];
  print "</td><td><div class='tt-admin-user-actions'>";
  print "<button class='ui-btn-inline ui-btn ui-mini'>send ID</button>";
  print "<button class='ui-btn-inline ui-btn ui-mini'>fix name</button>";
  print "<button class='ui-btn-inline ui-btn ui-mini'>fix email</button>";
  print "</div></td></tr>\n";
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
