<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); ?>

<script src="js/user_id_prompt.js?v=<?=rand()?>"></script>

</head>

<body>

<?=$tt_page_title?>

<?php
require("$dir/noscript.php");

if( isset($tt_error) )
{
  print "<h2 class=tt-error>$tt_error</h2>\n";
}
?>

<div data-role="collapsibleset">
  <div data-role="collapsible">
    <h2 id="new_survey">Start a new survey</h2> 
    <form id='new_survey_form' class='tt-form' method='post' action="tt.php">
      <input type=hidden name=action value=start>
      <p class='tt-form-instruction'>Before we get started, please provide your name and email address.</p>
      <label for="user_name">Name:</label>
      <input type="text" name="user_name" id="user_name" placeholder="Your name here..." autocomplete="off"
      <?php if( isset($name) ) { print " value='$name'"; } ?>) >
      <label for="user_email">Email:</label>
      <input type="text" name="user_email" id="user_email" placeholder="(optional)" autocomplete="off"
      <?php if( isset($email) ) { print " value='$email'"; } ?>) >
      <p class='tt-form-note'>The email address is optional. It will be used to send you a confirmation of your survey responses and a link that will allow you to review, complete, or update your responses.</p>
      <div class=submit><input id='start_survey_button' type="submit" value="Start Survey..."></div>
    </form>
  </div>
  <div data-role="collapsible">
    <h2>Resume an existing survey</h2>
      <form id="resume_survey_form" class='tt-form' method='post' action="tt.php" autocomplete="off">
      <input type=hidden name=action value=resume>
      <p class='tt-form-instruction'>Please enter the user ID issued when you started filling out the survey.</p>
      <p class='tt-form-note'>If you provided an email address, a copy should have been sent to you by email.</p>
      <label for='user_id'>User ID</label>
      <input type='text' name='user_id' id='user_id' placeholder="XXX-XXX-XXX-XXX" autocomplete="off"
      <?php if( isset($user_id) ) { print " value='$user_id'"; } ?>) >
      <p id='lost_user_id_help'>If you cannot find your user ID, please contact <?=$tt_poc_email_link?> to retrieve it.</p>
      <div class=submit><input id='resume_survey_button' type="submit" value="Resume Survey..."></div>
    </form>
  </div>
</div>

</body>
</html>
