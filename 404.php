<?php 
require_once(dirname(__FILE__).'/tt_init.php'); 
?>

<html>

<head>
  <title>404 Server Error</title>
</head>

<body bgcolor=white>
<?=$tt_page_title?>

<h1>404 Page Not found</h1>

<h2>What happened?</h2>
<ul>
<li>If you followed a link emailed to you, please contact <?=$tt_poc_email_link?> to get a valid link.</li>
<li>If you were just working inside <?=$tt_title?>, there is an internal error.  Please contact <?=$tt_poc_email_link?> to get this fixed.</li>
<li>If you entered this URL directly in your browser, you now know it doesn't work.</li>
</ul>

<br>

<img src='img/pac-404.png' height=300/ style="margin:1em 2em;">

<hr>

<?php
  echo "URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>\n";
  $fixer = "checksuexec ".escapeshellarg($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']);
  echo `$fixer`;
?>

</body>
</html>
