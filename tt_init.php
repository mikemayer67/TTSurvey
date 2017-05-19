<?php

session_start();

$tt_year = 2017;

$tt_poc = 'Mike Mayer';
$tt_poc_email = 'mikemayer67@vmwishes.com';
$tt_email_uri = "mailto:$tt_poc_email?Subject=".urlencode("Time & Talent User ID Request");
$tt_poc_email_link = "<a href='$tt_email_uri'>$tt_poc</a>";

$tt_title      = "CTS $tt_year Time &amp; Talent Survey";
$tt_page_title = "<h1 id='page_title'><img src='img/cts_logo.png' height=50>$tt_title</h1>";

$tt_max_gen_id_attempts = 256;

$tt_nojs = isset($_GET["nojs"]);

?>
