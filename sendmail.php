<?php

$dir = dirname(__FILE__);

require_once("$dir/db.php");

function email_welcome_info($uid,$name,$email,$year)
{
  global $tt_root_url;
  global $tt_active_year;
  global $tt_chair;
  global $tt_admin_email_link;
  global $tt_title;

  $rval = false;

  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "$tt_title Links";

    $query = "uid=$uid";
    if( isset($aid) && strlen($aid)>0 ) { $query .= "&amp;aid=$aid"; }

    $url = "$tt_root_url/tt.php?$query";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div><b>Welcome to the CTS $tt_title.</b></div>
      <div><br></div>
      <blockquote style='margin: 0 0 0 40px; border: none; padding: 0px;'>
      <div>Your UserID is $uid</div>
      <div><br></div>
      <div>Your name will appear on the survey as: $name</div>
      <div><br></div>
      <div>You can start your survey using the following link:</div>
      <div><a href='$url'>$url</a></div>
      <div><br></div>
      </blockquote>
      Thank you,
      <div><i>$tt_chair</i></div>
      <div><br></div>
      <div>For technical assistance with the online form, contact $tt_admin_email_link</div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <tt@ctslutheranelca.org>' . "\r\n";

    if( mail($to,$subject,$message,$headers) )
    {
      db_update_reminder($uid);
      error_log("Welcome email sent to $name ($to)");
      $rval = true;
    }
    else
    {
      error_log("Failed to send email to $name ($to)");
    }
  }
  else
  {
    error_log("No welcome email sent to $name ... no email address");
  }

  return $rval;
}

function email_survey_reminder($uid,$name,$email,$year)
{
  global $tt_root_url;
  global $tt_active_year;
  global $tt_chair;
  global $tt_admin_email_link;

  $rval = false;

  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "CTS $tt_title Reminder";

    $url = "$tt_root_url/tt.php?uid=$uid";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div>It's not too late to participate in the $year Time &amp; Talent survey.</div>
      <div><br></div>
      <div>You can start your survey using the following link: <a href='$url'>$url</a></div>
      <div><br></div>
      <div>Using this link will prepopulate the survey with your responses from last year where applicable.</div>
      <div><br></div>

      </blockquote>
      Thank you,
      <div><i>$tt_chair</i></div>
      <div><br></div>
      <div>For technical assistance with the online form, contact $tt_admin_email_link</div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <tt@ctslutheranelca.org>' . "\r\n";

    if( mail($to,$subject,$message,$headers) )
    {
      db_update_reminder($uid);
      error_log("Survey reminder sent to $name ($to)");
      $rval = true;
    }
    else
    {
      error_log("Failed to send survey reminder to $name ($to)");
    }
  }
  else
  {
    error_log("No survey reminder sent to $name ... no email address");
  }

  return $rval;
}

function email_unsubmitted_update_notifications($uid,$name,$email,$year)
{
  global $tt_root_url;
  global $tt_active_year;
  global $tt_chair;
  global $tt_admin_email_link;

  $rval = false;

  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "Unsubmitted Time & Talent Updates";

    $url = "$tt_root_url/tt.php?uid=$uid";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div>It appears that you made some changes on your $year Time &amp; Talent survey after submitting it.
      These changes will <b>not</b> be included in the summary report that will be made available to 
      council and coordinators unless you return to the form and hit the submit button (at the bottom of 
      the form).</div>
      <div><br></div>
      <div>You can revisit your survey using the following link: <a href='$url'>$url</a></div>
      <div><br></div>
      <div>If you had annonymous comments, you will need to find the email sent when you first
      submitted your responses.  There is no other way to reconnect you with your anonymous responses.</div>
      <div><br></div>

      </blockquote>
      Thank you,
      <div><i>$tt_chair</i></div>
      <div><br></div>
      <div>For technical assistance with the online form, contact $tt_admin_email_link</div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <tt@ctslutheranelca.org>' . "\r\n";

    if( mail($to,$subject,$message,$headers) )
    {
      db_update_reminder($uid);
      error_log("Unsubmitted updates email sent to $name ($to)");
      $rval = true;
    }
    else
    {
      error_log("Failed to send unsubmitted updates to $name ($to)");
    }
  }
  else
  {
    error_log("No unsubmitted updates email sent to $name ... no email address");
  }

  return $rval;
}

function email_unsubmitted_survey_notifications($uid,$name,$email,$year)
{
  global $tt_root_url;
  global $tt_active_year;
  global $tt_chair;
  global $tt_admin_email_link;

  $rval = false;

  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "Unsubmitted $tt_title";

    $url = "$tt_root_url/tt.php?uid=$uid";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div>It appears that you started your $year Time &amp; Talent survey but have not yet submitted it.
      Your response will <b>not</b> be included in the summary report that will be made available to 
      council and coordinators unless you return to the form and hit the submit button (at the bottom of 
      the form).</div>
      <div><br></div>
      <div>You can revisit your survey using the following link: <a href='$url'>$url</a></div>
      <div><br></div>
      <div><br></div>

      </blockquote>
      Thank you,
      <div><i>$tt_chair</i></div>
      <div><br></div>
      <div>For technical assistance with the online form, contact $tt_admin_email_link</div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <tt@ctslutheranelca.org>' . "\r\n";

    if( mail($to,$subject,$message,$headers) )
    {
      db_update_reminder($uid);
      error_log("Unsubmitted updates email sent to $name ($to)");
      $rval = true;
    }
    else
    {
      error_log("Failed to send unsubmitted updates to $name ($to)");
    }
  }
  else
  {
    error_log("No unsubmitted updates email sent to $name ... no email address");
  }

  return $rval;
}

function email_account_info($uid,$name,$email,$aid)
{
  global $tt_root_url;

  if( isset($email) && strlen($email)>0 )
  {
    $to = $email;
    $subject = "CTS $tt_title Links";

    $query = "uid=$uid";
    if( isset($aid) && strlen($aid)>0 ) { $query .= "&amp;aid=$aid"; }

    $url = "$tt_root_url/tt.php?$query";

    $message = "
      <html>
      <head>
      <meta http-equiv='Content-Type' content='text/html charset=us-ascii'>
      </head>
      <body style='word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;'>
      <div><br></div>
      <div><b>Welcome to the CTS $tt_title.</b></div>
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
      <div><i>$tt_chair</i></div>
      <div><br></div>
      <div>For technical assistance with the online form, contact $tt_admin_email_link</div>
      </body>
      </html>
      ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <tt@ctslutheranelca.org>' . "\r\n";

    mail($to,$subject,$message,$headers);
  }
}

function email_submission_feedback($uid,$email,$title,$poc,$poc_email,$ctsimg,$link1,$link2)
{
  $notice = "This is a multi-part message in MIME format.";
  
    $html = "
  <h1><img src='$ctsimg' height=50>$title</h1>
  <h2>Thank You!</h2>
  <h3><strong>Your survey results have been received and recorded.</strong></h3>
  <p>If you would like to revisit or revise your answers, you can come back at any time using the following link:</p>
  <p style='padding-left: 30px;'><a href='$link1'>$link1</a></p>";
  if(isset($link2)) {
    $html .= "
  <p>If you wish to review your anonymous responses as well, you must use the following link:</p>
  <p style='padding-left: 30px;'><a href='$link2'>$link2</a></p>
  <p style='padding-left: 30px;'><em>Note that this is the <strong>only</strong> way to associate you with your anonymous responses</em></p>";
  } 
  $html .= "
  <p>&nbsp;</p>
  <p>Alternatively, you can simply return to the $title website</p>
  <p style='padding-left: 30px;'>You will need to enter your User ID: <strong>$uid</strong>.</p>
  <p>&nbsp;</p>
  <p>If you lose " 
  . (isset($link2) ? "these links" : "this link" ) 
  . " you can contact $poc at $poc_email to look up your user ID.</p>
  ";
  
  $text = "
  $title
  
  THANK YOU!
  
  Your survey results have been received and recorded.
  
  If you would like to revisit or revise your answers, you can come back at any time using the following link:
     $link1
  ";
  if(! isset($link2) ) {
    $text .= "
  If you wish to review your anonymous responses as well, you must use the following link:</p>
     $link2
      <em>Note that this is the <strong>only</strong> way to associate you with your anonymous responses</em></p>
  ";
  }
  $text .= "
  Alternatively, you can simply return to the $title website
    You will need to enter your User ID: $uid
  
  If you lose "
  . (isset($link2) ? "these links" : "this link" ) 
  . " you can contact $poc at $poc_email to look up your user ID.
  ";


  $semi_rand = md5(time());
  $mime_boundary = "==MULTIPART_BOUNDARY_$semi_rand";
  $mime_boundary_header = chr(34) . $mime_boundary . chr(34);

  $body = "$notice

--$mime_boundary
Content-Type: text/plain; charset=us-ascii
Content-Transfer-Encoding: 7bit

$text

--$mime_boundary
Content-Type: text/html; charset=us-ascii
Content-Transfer-Encoding: 7bit

$html

--$mime_boundary--";

  // Always set content-type when sending HTML email

  // More headers
  $headers  = 'From: <tt@ctslutheranelca.org>' . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: multipart/alternative;\r\n";
  $headers .= "     boundary=" . $mime_boundary_header;

  $subject = "CTS $tt_title Submission";

  $rc = mail($email,$subject,$body,$headers);
  if( $rc )
  {
    error_log("INFO: confirmation email sent to $email ($subject)");
  }
  else
  {
    error_log("FAIL: failed to send confirmation email to $email ($subject)");
  }


}
