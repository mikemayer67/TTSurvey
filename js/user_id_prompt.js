var email_is_good = false;
var name_is_good  = false;
var id_is_good  = false;

$(function() {
  validate_user_email();
  validate_user_name();
  validate_user_id();

  $('#user_email').on('input',validate_user_email);
  $('#user_name').on('input',validate_user_name);
  $('#user_id').on('input',validate_user_id);

} );

function validate_user_name()
{
  var input = $('#user_name');
  var name  = input.val();
  var names = name.match(/\b[a-z]+[a-z\.]\b/gi);

  name_is_good = names && names.length>1;
  
  var name_error = $('#name_error');
  var has_name_error = name_error.length > 0;

  if(name_is_good) {
    set_state(input,'tt-valid');
    name_error.remove();
  } else if (name.length == 0) {
    set_state(input,'tt-pending');
    name_error.remove();
  } else {
    set_state(input,'tt-pending');
    if( ! has_name_error ) {
      var name_error = $('<p/>', { id:'name_error', class:'tt-pending', for:'user_name' ,
                         text:'Pleae provide a first and last name'} );
      input.parent().after(name_error);
    }
  }

  check_start_survey_state();
}

function validate_user_email()
{
  var input = $('#user_email');
  var re = /^\s*([^\s@]+@[^\s@]+\.[^\s@]+)*\s*$/;
  var email = input.val();
  email_is_good = re.test(email);

  if(email_is_good) {
    set_state(input,'tt-valid');
    $('#email_error').remove()
  } else {
    var ui_err = $('#email_error');
    if( ui_err.length == 0 ) {
      ui_err = $('<p/>', {id:'email_error', for:'user_email'} );
        input.parent().after(ui_err);
    }

    var temail = email;
    if( temail.search(/@/) == -1 )     { temail = temail.concat('@xxx.xxx'); }
    if( temail.search(/@\w+\./) == -1 ) { temail = temail.concat('xxx.xxx');  }

    if( re.test(temail) ) {
      set_state(ui_err,'tt-pending');
      set_state(input,'tt-pending');
      ui_err.text('Incomplete email address');
    } else {
      set_state(ui_err,'tt-invalid');
      set_state(input,'tt-invalid');
      ui_err.text('Invalid email address');
    }
  }
  check_start_survey_state();
}

function validate_user_id()
{
  var input = $('#user_id');
  var msg;
  var state;
  var id_is_ok = false;

  var id = input.val();
  id = id.replace(/^\s*/, '');
  id = id.replace(/\s*$/, '');

  var re = /^(?:\w{3}-){3}\w{3}$/;

  if ( id.length > 15 ) {
    state = 'tt-invalid';
    msg   = 'User ID is too long';
  } else {
    var tmpl = "000-000-000-000";
    var tid = id.concat(tmpl.substr(id.length,15));
    if( re.test(tid) ) {
      state = 'tt-pending';
      if( id.length == 15 ) { 
        id_is_ok = true;
        msg = 'Validating User ID'; 
      } else { 
        msg = 'Incomplete User ID' 
      }
    } else {
      state = 'tt-invalid';
      msg   = 'Invalid User ID format';
    }
  }

  $('#resume_survey_button').closest('div.submit').hide(400);

  var ui_err = $('#user_id_error');
  var has_ui_err = ui_err.length > 0;

  $('#user_id_info').remove()
  $('#lost_user_id_help').show();

  set_state(input, state);

  if( id.length == 0 ) {

    if( has_ui_err ) { ui_err.remove(); }

    set_state(input,'tt-valid');

  } else {

    if( has_ui_err ) {
      set_state(ui_err, state);
      ui_err.text(msg);
      ui_err.show();
    } else {
      ui_err = $('<p/>', { id:'user_id_error', for:'user_id', class:state, text:msg } );
        input.parent().after(ui_err);
    }

    if( id_is_ok ) {
      $.ajax( {
        type: 'GET',
        url:  'ajax/validate.php',
        data: { user_id: id },
        success: function(data) {
          set_state(input,'tt-valid');
          ui_err.remove();

          var ui_info = $('<table/>', { id:'user_id_info'} );

          var row;
          var cell;

          row = $('<tr/>');
          ui_info.append(row);
          cell = $('<td/>', {class:'tt-ui-label-cell', html:'Name:'} );
          row.append(cell);
          cell = $('<td/>', {class:'tt-ui-value-cell', html:data['name']} );
          row.append(cell);

          row = $('<tr/>');
          ui_info.append(row);
          cell = $('<td/>', {class:'tt-ui-label-cell', html:'Email:'} );
          row.append(cell);
          cell = $('<td/>', {class:'tt-ui-value-cell', html:data['email'] } );
          row.append(cell);

          input.parent().after(ui_info);
          $('#lost_user_id_help').hide();

          $('#resume_survey_button').closest('div.submit').show(400);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          set_state(input,'tt-invalid');
          set_state(ui_err,'tt-invalid');
          ui_err.text(jqXHR.statusText);
        }
      } )
    }
  }
}

function set_state(item,state)
{
  item.removeClass('tt-invalid').removeClass('tt-invalid').removeClass('tt-pending').addClass(state);
}

function check_start_survey_state()
{
  if( email_is_good && name_is_good ) {
    $('#start_survey_button').closest('div.submit').show(400);
  } else {
    $('#start_survey_button').closest('div.submit').hide(400);
  }
}
