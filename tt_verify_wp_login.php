<?php

$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
foreach ($cookies as $cookie)
{
  $kv = explode('=',trim($cookie),2);
  if(count($kv) == 2)
  {
    if(preg_match('/^wordpress_logged_in/',$kv[0])) { return; }
  }
}


print "<!DOCTYPE html>\n";
print "<html>\n";
print "<head>\n";

require("$dir/tt_head.php");
$v = rand();

?>

  <link rel='stylesheet' type='text/css' href='tt_menu.css?v=<?=$v?>'>

</head>
<body>
<h1><img src='img/cts_logo.png' height=50><?=$tt_title?> Result Summary</h1>

<h3>You must be logged into the <a href='http://ctslutheranelca.org'>CTS Website</a> to view the summary data</h3>
</body>

<?php exit(0); ?>

