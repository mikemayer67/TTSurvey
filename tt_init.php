<?php

if( session_status() == PHP_SESSION_NONE)
{
  session_start();
}

require_once(dirname(__FILE__).'/db.php');

$tt_delog       = 0;

$tt_admin       = 'Mike Mayer';
$tt_admin_email = 'mikemayer67@vmwishes.com';
$tt_admin_uri   = "mailto:$tt_admin_email?Subject=".urlencode("Time & Talent Issue");
$tt_admin_email_link = "<a href='$tt_admin_uri'>$tt_admin</a>";

$tta_reminder_frequency = 12*3600;  // 12 hours (and yes, this is a period, not frequency)


date_default_timezone_set('EST5EDT');

try
{
  $statics = db_active_statics();

  $tt_active_year = $statics['year'];
  $tt_chair_id    = $statics['chair_id'];
  $tt_admin_id    = $statics['admin_id'];
  $tt_poc_email   = $statics['poc_email'];
  $tt_delog       = $statics['delog'];

  if( $tt_chair_id === NULL ) { $tt_chair_id = $tt_admin_id; }

  $chair_info = db_get_user_info($tt_chair_id);
  $admin_info = db_get_user_info($tt_admin_id);

  $tt_admin = $admin_info['name'];
  $tt_chair = $chair_info['name'];

  $tt_admin_email = $admin_info['email'];
  $tt_chair_email = $chair_info['email'];

  if( $tt_chair === $tt_admin ) { $tt_poc = $tt_admin; }
  else                          { $tt_poc = "$tt_chair and $tt_admin"; }

  if( $tt_poc_email === NULL ) $tt_poc_email = $tt_admin_email;
  if( $tt_poc_email === NULL ) $tt_poc_email = $tt_chair_email;

  $tt_year = $tt_active_year;

  if(isset($_REQUEST['year'])) { $tt_year = $_REQUEST['year']; }

  $tt_title = "$tt_year Time & Talent Survey";

  $year_statics = db_get_statics($tt_year);
  if( isset($year_statics['campaign_title']) ) { $tt_title = $year_statics['campaign_title']; }
  if( isset($year_statics['comments_label']) ) { $tt_comments_label = $year_statics['comments_label']; }

  $tt_poc_email_uri = "mailto:$tt_poc_email?Subject=".urlencode($tt_title); 
  $tt_poc_email_link = "<a href='$tt_poc_email_uri'>$tt_poc</a>";

  $tt_chair_email_uri = "mailto:$tt_chair_email?Subject=".urlencode($tt_title);
  $tt_chair_email_link = "<a href='$tt_chair_email_uri'>$tt_chair</a>";

  $tt_admin_email_uri = "mailto:$tt_admin_email?Subject=".urlencode($tt_title);
  $tt_admin_email_link = "<a href='$tt_admin_email_uri'>$tt_admin</a>";

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
  echo "<h1 class='tt-title'><img src='img/cts_logo.png' height=50>$tt_title</h1>";
  echo "<h1>500 Server Error</h1>\n";
  echo "A misconfiguration on the server caused a hiccup.<br>\n";
  echo "Please contact $tt_admin_email_link to investigate the error.<br>\n";
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
