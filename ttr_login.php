<?php

if( isset( $_POST['ttr_authenticated'] ) )
{
  $_SESSION['ttr_authenticated'] = $_POST['ttr_authenticated'];
  setcookie('ttr_authenticated','1', time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
  return;
}
if( isset( $_SESSION['ttr_authenticated'] ) )
{
  return;
}
if( isset( $_COOKIE['ttr_authenticated'] ) )
{
  $_SESSION['ttr_authtenticated'] = $_COOKIE['ttr_authenticated'];
  return;
}

print "<!DOCTYPE html>\n";
print "<html>\n";
print "<head>\n";

require("$dir/tt_head.php");
$v = rand();

print "<link rel='stylesheet' type='text/css' href='ttr.css?v=$v'>";
print "<script src='js/ttr_login.js?v=$v'></script>\n";

?>

</head>
<body>

<h1><img src='img/cts_logo.png' height=50><?=$tt_title?> Result Summary</h1>

<div class=ttr-login-block>
<h2>You must be logged in to view the summary data</h2>
<div>
<label for="ttr-passwd">Enter Password</label>
<input type="password" name="ttr-passwd" id="ttr-passwd" class='ttr-pending' value="">
</div>
<div>
<button id='ttr-login-submit' class='ui-btn ui-btn-b ui-corner-all' name=Continue>Continue</button>
<?php
// <a hroef='#' id="ttr-login-submit" class="ui-btn ui-btn-b ui-corner-all mc-top-margin-1-5">Continue</a>
?>
</div>
</div>

</body>
</html>

<?php exit(0) ?>
