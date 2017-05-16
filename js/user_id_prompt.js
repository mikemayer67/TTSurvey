var email_is_good = false;
var name_is_good  = false;
var id_is_good  = false;

$(function() {
  validate_user_email()
  validate_user_name()
  validate_user_id()

  $('#user_email').on('input',validate_user_email);
  $('#user_name').on('input',validate_user_name);
  $('#user_id').on('input',validate_user_id);

} );

function validate_user_email()
{
  var input = $('#user_email');
  var re = /^\s*([^\s@]+@[^\s@]+\.[^\s@]+)*\s*$/;
  var email = input.val();
  email_is_good = re.test(email);
  if(email_is_good) {
    input.removeClass("tt-invalid").addClass("tt-valid");
  } else {
    input.removeClass("tt-valid").addClass("tt-invalid");
  }
  check_start_survey_state();
}

function validate_user_name()
{
  var input = $('#user_name');
  var re = /^(\w{2,}\s+\w{2,})$/;
  var name = input.val();
  name_is_good = re.test(name);

  if(name_is_good) {
    input.removeClass("tt-invalid").addClass("tt-valid");
  } else {
    input.removeClass("tt-valid").addClass("tt-invalid");
  }
  check_start_survey_state();
}

function validate_user_id()
{
  var input = $('#user_id');
  var re = /^\s*(?:[a-zA-Z0-9]{3}-){3}[a-zA-Z0-9]{3}\s*$/;
  var id = input.val();
  id_is_good = re.test(id);

  if(id_is_good) {
    input.removeClass("tt-invalid").addClass("tt-valid");
  } else {
    input.removeClass("tt-valid").addClass("tt-invalid");
  }
  check_resume_survey_state();
}

function check_start_survey_state()
{
  if( email_is_good && name_is_good ) {
    $('#start_survey_button').closest('div.submit').show(400);
  } else {
    $('#start_survey_button').closest('div.submit').hide(400);
  }
}

function check_resume_survey_state()
{
  if( id_is_good ) {
    $('#resume_survey_button').closest('div.submit').show(400);
  } else {
    $('#resume_survey_button').closest('div.submit').hide(400);
  }
}
