<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); 

require_once("$dir/db.php");
$user_uid = $_SESSION['USER_ID'];
$user_info = db_user_info($user_uid);
$user_name = $user_info['name'];
$user_email = (isset($user_info['email']) ? $user_info['email'] : "(unspecified)");
?>

<script src="js/survey.js?v=<?=rand()?>"></script>

</head>

<body>

<div id=tt_survey_header>
<span id=tt_user_uid class=tt-user-info>User ID: <span><?=$user_uid?></span>
  <button data-role='none'>logout</button></span>
<span id=tt_user_name class=tt-user-info>Name: <span><?=$user_name?></span>
  <input data-role='none' placeholder='Name...' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
<span id=tt_user_email class=tt-user-info>Email: <span><?=$user_email?></span>
  <input data-role='none' placeholder='(optional)' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
</div>

</form>

<?=$tt_page_title?>

<h2>SURVEY DUDE...</h2>
UserID: <?=$user_id?>

</body>
</html>

