var user_name;
var user_email;

var user_edit_timeout;

$(function() {
  $('#tt_user_name button').click(start_user_name_edit);
  $('#tt_user_email button').click(start_user_email_edit);
  $('#tt_user_uid button').click(logout_user);

  user_name  = $('#tt_user_name span').text(); 
  user_email = $('#tt_user_email span').text(); 
} );

function logout_user()
{
  window.location='tt_logout.php';
}


function start_user_name_edit()
{
  $('#tt_user_name span').hide();
  $('#tt_user_name input').val(user_name).show();
  $('#tt_user_name button').text('set').prop('disabled',true);

  clearTimeout(user_edit_timeout);
  user_edit_timeout = setTimeout(auto_close_edits,3000);
}

function stop_user_name_edit()
{
  $('#tt_user_name span').show();
  $('#tt_user_name input').hide();
  $('#tt_user_name button').text('fix').prop('disabled',false);
}


function start_user_email_edit()
{
  $('#tt_user_email span').hide();
  $('#tt_user_email input').show();
  $('#tt_user_email button').text('set').prop('disabled',true);

  if(user_email === undefined) {
    
  }

  clearTimeout(user_edit_timeout);
  user_edit_timeout = setTimeout(auto_close_edits,15000);
}

function stop_user_email_edit()
{
  $('#tt_user_email span').show();
  $('#tt_user_email input').hide();
  $('#tt_user_email button').text('fix').prop('disabled',false);
}


function auto_close_edits()
{
  stop_user_name_edit();
  stop_user_email_edit();
}

