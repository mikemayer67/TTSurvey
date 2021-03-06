<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); ?>

</head>

<body>

<?php
require("$dir/tt_page.php");
?>

<h2 id="confirm_userid">Confirm User Info</h2>
<form id="confirm_user_form" class='tt-form' method='post' action='tt.php'>
  <input type=hidden name=confirmed value='<?=$user_id?>'>
<?php if( isset($anon_id) ) { ?>
  <input type=hidden name=anon_id value='<?=$anon_id?>'>
<?php } ?>
  <input type=hidden name=user_name value='<?=$user_name?>'>
  <input type=hidden name=user_email value='<?=$user_email?>'>
  <p class='tt-form-instruction'>Before we get started, please confirm that I have the correct survey</p>
  <table id='user_id_info'>
  <tr><td class=tt-ui-label-cell>Name:</td><td class=tt-ui-value-cell><?=$user_name?></td></tr>
  <tr><td class=tt-ui-label-cell>Email:</td><td class=tt-ui-value-cell><?=$user_email?></td></tr>
  <tr><td class=tt-ui-label-cell>User ID:</td><td class=tt-ui-value-cell><?=$user_id?></td></tr>
  </table>
  <p class='tt-form-note'>If there are errors in your name or email address, you will be able to fix them in the survey form itself.  For now, we just need to confirm that I'm about to grab the correct survey.</p>
  <div class='submit'><input id=start_survey_button' type='submit' name=confirm value="Yep... That's me"></div>
  <div class='submit'><input id=start_survey_button' type='submit' name=cancel value="Nope... Not me."></div>
</form>

</body>
</html>
