<?php 
require_once('tt_init.php'); 
?>

<html><head><title>500 Server Error</title></head>

<body bgcolor=white>
<?=$tt_page_title?>

<h1>500 Server Error</h1>

A misconfiguration on the server caused a hiccup.

<br>

Please contact <?=$tt_poc_email_link?> to investigate the error.

<br>

<center><img src='img/sick-computer.jpg'/></center>

<hr>

<?php
  echo "URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>\n";
  $fixer = "checksuexec ".escapeshellarg($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']);
  echo `$fixer`;
?>

</body>
</html>
