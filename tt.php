<?php

session_start();

$year = 2017;
$tt_poc = 'Mike Mayer';
$tt_poc_email = 'mikemayer67@vmwishes.com';
$tt_email_uri = "mailto:$tt_poc_email?Subject=".urlencode("Time & Talent User ID Request");
$tt_poc_email_link = "<a href='$tt_email_uri'>$tt_poc</a>";

$tt_title      = "CTS $year Time &amp; Talent Survey";
$tt_page_title = "<h1 id='page_title'><img src='img/cts_logo.png' height=50>$tt_title</h1>";

$tt_nojs = isset($_GET["nojs"]);

if( isset($_SESSION['USER_ID']) )
{
  setcookie('USER_ID', $_SESSION['USER_ID'], time()+30*86400);
  require(dirname(__FILE__).'/survey.php');
}
else if (isset($_COOKIE['USER_ID']) )
{
  require(dirname(__FILE__).'/user_id_verify.php');
}
else
{
  require(dirname(__FILE__).'/user_id_prompt.php');
}

?>
