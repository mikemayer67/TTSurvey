<?php

session_start();

require_once(dirname(__FILE__).'/db.php');

$tt_poc       = 'Mike Mayer';
$tt_poc_email = 'mikemayer67@vmwishes.com';
$tt_email_uri = "mailto:$tt_poc_email?Subject=".urlencode("Time & Talent User ID Request");
$tt_poc_email_link = "<a href='$tt_email_uri'>$tt_poc</a>";
$tt_delog     = 0;

$tta_reminder_frequency = 12*3600;  // 12 hours (and yes, this is a period, not frequency)

date_default_timezone_set('EST5EDT');

try
{
  $statics = db_active_survey_statics();

  $tt_active_year = $statics['year'];
  $tt_poc         = $statics['admin_name'];
  $tt_poc_email   = $statics['admin_email'];
  $tt_delog       = $statics['delog'];

  $tt_year = $tt_active_year;
  if(isset($_REQUEST['year']))
  {
    $tt_year = $_REQUEST['year'];
  }

  $tt_email_uri = "mailto:$tt_poc_email?Subject=".urlencode("Time & Talent User ID Request");
  $tt_poc_email_link = "<a href='$tt_email_uri'>$tt_poc</a>";

  $tt_title      = "CTS $tt_year Time &amp; Talent Survey";
  $tt_page_title = "<h1 class='tt-title'><img src='img/cts_logo.png' height=50>$tt_title</h1>";

  $tt_root_url = 'http://'.$_SERVER['SERVER_NAME'];
  if( ! preg_match('/stewardship/',$tt_root_url) )
  {
    $tt_root_url .= '/stewardship';
  }
  $tt_root_url = preg_replace('/\/$/','',$tt_root_url);
}
catch (Exception $e)
{
  $msg  = $e->getMessage();
  $file = $e->getFile();
  $line = $e->getLine();

  error_log("${file}[$line]: $msg");

  echo "<html><head><title>500 Server Error</title></head>\n";
  echo "<body bgcolor=white>\n";
  echo "<h1 class='tt-title'><img src='img/cts_logo.png' height=50>CTS Time &amp; Talent Survey</h1>";
  echo "<h1>500 Server Error</h1>\n";
  echo "A misconfiguration on the server caused a hiccup.<br>\n";
  echo "Please contact $tt_poc_email_link to investigate the error.<br>\n";
  echo "<img src='img/sick-computer.jpg' height=300/ style='margin:1em 2em;'>\n";
  echo "<hr>\n";
  echo "URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>\n";
  $fixer = "checksuexec ".escapeshellarg($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']);
  echo `$fixer`;
  echo "</body>\n";
  echo "</html>\n";

  exit;
}

?>
