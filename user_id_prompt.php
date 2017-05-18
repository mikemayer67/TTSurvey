<!DOCTYPE html>
<html>
<head>
<?php require(dirname(__FILE__).'/tt_head.php'); ?>

<script src="js/user_id_prompt.js?v=<?=rand()?>"></script>

</head>

<body>

<?=$tt_page_title?>

<?php if( ! $tt_nojs ) { ?>
<noscript>
    <p>This site is best viewed with Javascript.<br class='nospace'>
    If you are unable to turn on Javascript, please consider using the <a href="tt.php?nojs">Non-Javascript variation</a> of this form.</p>
</noscript>
<?php } ?>

<div data-role="collapsibleset">
  <div data-role="collapsible">
    <h2 id="new_survey">Start a new survey</h2> 
    <form id='new_survey_form' class='tt-form' method="post" action="start_new_survey.php">
      <p class='tt-form-instruction'>Before we get started, please provide your name and email address.</p>
      <label for="user_name">Name:</label>
      <input type="text" name="user_name" id="user_name" placeholder="Your name here..." autocomplete="off">
      <?php if($tt_nojs) { print "<br class='nospace'>"; }; ?>
      <label for="user_email">Email:</label>
      <input type="text" email="user_email" id="user_email" placeholder="(optional)" autocomplete="off">
      <p class='tt-form-note'>The email address is optional. It will be used to send you a confirmation of your survey responses and a link that will allow you to review, complete, or update your responses.</p>
      <div class=submit><input id='start_survey_button' type="submit" value="Start Survey..."></div>
    </form>
  </div>
  <div data-role="collapsible">
    <h2>Resume an existing survey</h2>
      <form id="resume_survey_form" class='tt-form' method='post' action="resume_new_survey.php" autocomplete="off">
      <p class='tt-form-instruction'>Please enter the user ID issued when you started filling out the survey.</p>
      <p class='tt-form-note'>If you provided an email address, a copy should have been sent to you by email.</p>
      <label for='user_id'>User ID</label>
      <input type='text' name='user_id' id='user_id' placeholder="XXX-XXX-XXX-XXX" autocomplete="off">
      <p id='lost_user_id_help'>If you cannot find your user ID, please contact <?=$tt_poc_email_link?> to retrieve it.</p>
      <div class=submit><input id='resume_survey_button' type="submit" value="Resume Surey..."></div>
    </form>
  </div>
</div>

<script>
</script>

</body>
</html>
