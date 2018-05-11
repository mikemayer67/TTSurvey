<?php

// Check to see if the summary password has
//   already been enterred.

$ttr_passwd = null;

if(isset($_POST['ttr_passwd']))
{
  $ttr_passwd = $_POST['ttr_passwd'];
}
else if(isset($_SESSION['ttr_passwd']))
{
  $ttr_passwd = $_SESSION['ttr_passwd'];
}
else if(isset($_COOKIE['ttr_passwd']))
{
  $ttr_passwd = $_COOKIE['ttr_passwd'];
}

if(isset($ttr_passwd))
{
  $statics = db_active_statics();
  $result_pwd = $statics['result_pwd'];

  if( strcmp($ttr_passwd, $result_pwd) == 0 )
  {
    $_SESSION['ttr_passwd'] = $ttr_passwd;
    setcookie('ttr_passwd',$ttr_passwd, time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    return;
  }
}
else
{
  unset($_SESSION['ttr_passwd']);
}

// If user already logged in as admin, go ahead and let
//   them access the summary (and set ttr_passwd for them)

$tta_passwd = null;

if(isset($_POST['tta_passwd']))
{
  $tta_passwd = $_POST['tta_passwd'];
}
else if(isset($_SESSION['tta_passwd']))
{
  $tta_passwd = $_SESSION['tta_passwd'];
}
else if(isset($_COOKIE['tta_passwd']))
{
  $tta_passwd = $_COOKIE['tta_passwd'];
}

if(isset($tta_passwd))
{
  $statics = db_active_statics();
  $admin_pwd = $statics['admin_pwd'];
  $result_pwd = $statics['result_pwd'];

  if( strcmp($tta_passwd, $admin_pwd) == 0 )
  {
    $_SESSION['ttr_passwd'] = $result_pwd;
    setcookie('ttr_passwd',$result_pwd, time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    return;
  }
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
