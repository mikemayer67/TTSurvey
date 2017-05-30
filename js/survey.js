
var no_email = '(unspecified)';

var user_name;
var user_email;

var user_edit_timeout;

$(function() {
  stop_user_name_edit();
  stop_user_email_edit();

  $('#tt_user_uid button').on('click',logout_user);

  user_name  = $('#tt_user_name span').text(); 
  user_email = $('#tt_user_email span').text(); 

  if( user_email.length == 0 ) {
    $('#tt_user_email span').text(no_email);
  }

  if( window.orientation == 0 ) { 
    $('#tt_landscape_note').show(); 
    $('#tt_survey_header').css('margin-top', $('#tt_landscape_note').height() );
  }

  $(window).on('orientationchange', function(e) {
    if( window.orientation == 0 ) { 
      $('#tt_landscape_note').show(); 
      $('#tt_survey_header').css('margin-top', $('#tt_landscape_note').height() );
    }
    else { 
      $('#tt_landscape_note').hide() 
      $('#tt_survey_header').css('margin-top',0);
    }
  } );

  $('input[type=checkbox][data-tt-children]')
  .on('click',toggle_children)
  .each(toggle_children);
  
  $('input[type=checkbox][data-tt-qual]')
  .on('click',toggle_qual)
  .each(toggle_qual);

  $('input[type=checkbox]').on('click',submit_checkbox_item);
  $('textarea').on('change',submit_text_item);
  
} );

function logout_user()
{
  window.location='tt_logout.php';
}

function start_user_name_edit()
{
  $('#tt_user_name span').hide();
  $('#tt_user_name input').val(user_name).show().on('input',validate_user_name);
  $('#tt_user_name button').text('set').prop('disabled',true).off().on('click',submit_user_name);

  restart_edit_timeout();
}

function stop_user_name_edit()
{
  $('#tt_user_name span').show();
  $('#tt_user_name input').hide();
  $('#tt_user_name button').text('fix').prop('disabled',false).off().on('click',start_user_name_edit);
}


function start_user_email_edit()
{
  var email = (user_email === undefined ? '' : user_email);

  $('#tt_user_email span').hide();
  $('#tt_user_email input').val(email).show().on('input',validate_user_email);
  $('#tt_user_email button').text('set').prop('disabled',true).off().on('click',submit_user_email);

  restart_edit_timeout();
}

function stop_user_email_edit()
{
  $('#tt_user_email span').show();
  $('#tt_user_email input').hide();
  $('#tt_user_email button').text('fix').prop('disabled',false).off().on('click',start_user_email_edit);;
}

function restart_edit_timeout()
{
  clearTimeout(user_edit_timeout);
  user_edit_timeout = setTimeout(auto_close_edits,15000);
}

function auto_close_edits()
{
  stop_user_name_edit();
  stop_user_email_edit();
}

function validate_user_name()
{
  restart_edit_timeout();

  var input = $('#tt_user_name input');
  var name  = input.val();
  var names = name.match(/\b[a-z]+[a-z\.]\b/gi);

  var is_good = names && names.length>1;
  
  if(is_good) {
    set_state(input,'tt-valid');
    $('#tt_user_name button').prop('disabled',false);
  } else {
    set_state(input,'tt-invalid');
    $('#tt_user_name button').prop('disabled',true);
  }
}

function validate_user_email()
{
  restart_edit_timeout();

  var input = $('#tt_user_email input');
  var re = /^\s*([^\s@]+@[^\s@]+\.[^\s@]+)*\s*$/;
  var email = input.val();
  var is_good = re.test(email);
  
  if(is_good) {
    set_state(input,'tt-valid');
    $('#tt_user_email button').prop('disabled',false);
  } else {
    set_state(input,'tt-invalid');
    $('#tt_user_email button').prop('disabled',true);
  }
}

function submit_user_name()
{
  restart_edit_timeout();

  var input = $('#tt_user_name input');
  var name = input.val();

  set_state(input,'tt-pending');
  $('#tt_user_name button').prop('disabled',true);
 
  $.ajax( {
    type: 'POST',
    url:  'ajax_update_user_name.php',
    data: { user_name: name },
  } )
    .done( function() {
      user_name = name;
      $('#tt_user_name span').text(user_name);
    } )
    .fail( function() {
      alert('Failed to update your name in the database');
    } )
    .always( function() {
      stop_user_name_edit();
    } );
}

function submit_user_email()
{
  restart_edit_timeout();

  var input = $('#tt_user_email input');
  var email = input.val();

  set_state(input,'tt-pending');
  $('#tt_user_email button').prop('disabled',true);

  $.ajax( {
    type: 'POST',
    url:  'ajax_update_user_email.php',
    data: { user_email: email },
  } )
    .done( function() {
      user_email = email;
      $('#tt_user_email span').text(user_email.length>0 ? user_email : no_email);
    } )
    .fail( function() {
      alert('Failed to update your email in the database');
    } )
    .always( function() {
      stop_user_email_edit();
    } );
}

function set_state(item,state)
{
  item.removeClass('tt-invalid').removeClass('tt-invalid').removeClass('tt-pending').addClass(state);
}

function toggle_children()
{
  var children = $(this).attr('data-tt-children');

  if( $(this).is(':checked') ) { 
    $(children).prop('disabled',false)
    .each( function() { $(this).prop('checked', $(this).data('cached') ) } );
  }
  else { 
    $(children).prop('disabled',true)
    .each( function() { $(this).data('cached', $(this).prop('checked') ) } )
    .prop('checked',false);
  }
}

function toggle_qual()
{
  var qual = $(this).attr('data-tt-qual');

  if( $(this).is(':checked') ) { 
    $(qual).show("fast");
  }
  else { 
    $(qual).hide("fast");
  }
}

function submit_checkbox_item()
{
  var id    = $(this).attr('id');
  var value = $(this).is(':checked') ? 1 : 0;

  var keys = id.split('_');

  $.ajax( {
    type: 'POST',
    url:  'ajax_update_survey_item.php',
    data: { keys: keys, value: value },
  } );
}

function submit_text_item()
{
  var id    = $(this).attr('id');
  var value = $(this).val();

  var keys = id.split('_');

  var anon_id = "#anon_" + id;

  var anon = $(anon_id).is(':checked');

  $.ajax( {
    type: 'POST',
    url:  'ajax_update_survey_item.php',
    data: { keys: keys, value: value, anon: anon },
  } );
}
