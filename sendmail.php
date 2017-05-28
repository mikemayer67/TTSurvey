<?php

function email_account_info($uid,$name,$email,$aid)
{
  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "Time & Talent Survey Links";

    $query = "uid=$uid";
    if( isset($aid) && strlen($aid)>0 ) { $query .= "&amp;aid=$aid"; }

    $host = $_SERVER['SERVER_NAME'];
    $url  = "http://$host/stewardship/tt.php?$query";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div><b>Welcome to the 2017 CTS Time &amp; Talent Survey.</b></div>
      <div><br></div>
      <blockquote style='margin: 0 0 0 40px; border: none; padding: 0px;'>
      <div>Your UserID is $uid</div>
      <div><br></div>
      <div>Your name will appear on the survey as: $name</div>
      <div><br></div>
      <div>You can revisit (or complete) your survey using the following link:</div>
      <div><a href='$url'>$url</a></div>
      <div><br></div>
      </blockquote>
      Thank you,
      <div><i>mike mayer</i></div>
      <div><br></div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <stewardship@ctslutheranelca.org>' . "\r\n";

    mail($to,$subject,$message,$headers);
  }
}

?>
