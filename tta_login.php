<?php
// Note that this uses the same css file (ttr.ccs) as the summary page
// While all IDs and variable names use the 'tta' prefix,
//   css classes use the 'ttr' prefix

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
  $result_pwd = $statics['admin_pwd'];

  if( strcmp($tta_passwd, $result_pwd) == 0 )
  {
    $_SESSION['tta_passwd'] = $tta_passwd;
    setcookie('tta_passwd',$tta_passwd, time()+30*86400, '/', '.'.$_SERVER['SERVER_NAME'], false, true);
    return;
  }
}
else
{
  unset($_SESSION['tta_passwd']);
}


print "<!DOCTYPE html>\n";
print "<html>\n";
print "<head>\n";

require("$dir/tt_head.php");
$v = rand();

print "<link rel='stylesheet' type='text/css' href='ttr.css?v=$v'>";
print "<script src='js/tta_login.js?v=$v'></script>\n";

?>

</head>
<body>

<h1><img src='img/cts_logo.png' height=50><?=$tt_title?> Admin Functions</h1>

<div class=ttr-login-block>
<h2>You must be logged in as admin to continue</h2>
<div>
<label for="tta-passwd">Enter Password</label>
<input type="password" name="tta-passwd" id="tta-passwd" class='ttr-pending' value="">
</div>
<div>
<button id='tta-login-submit' class='ui-btn ui-btn-b ui-corner-all' name=Continue>Continue</button>
<?php
// <a hroef='#' id="tta-login-submit" class="ui-btn ui-btn-b ui-corner-all mc-top-margin-1-5">Continue</a>
?>
</div>
</div>

</body>
</html>

<?php exit(0) ?>
