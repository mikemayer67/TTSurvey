$(function() {
  validate_passwd();

  $('#ttr-passwd').on('input',validate_passwd);
  $('#ttr-login-submit').on('click',goto_summary);
});

function validate_passwd()
{
  var input = $('#ttr-passwd');
  var passwd = input.val();

  if(passwd.length == 0) {
    $('#ttr-passwd').removeClass('ttr-invalid').removeClass('ttr-valid').addClass('ttr-pending');
    $('#ttr-login-submit').hide();
  }
  else {
    $('#ttr-login-submit').hide();
    $.ajax( {
      type: 'POST',
      url: 'ajax/validate_ttr_login.php',
      data: { value: passwd },
    })
    .done( function(data) {
      if( data['valid'] ) {
        $('#ttr-passwd').removeClass('ttr-pending').removeClass('ttr-invalid').addClass('ttr-valid');
        $('#ttr-login-submit').show();
      }
      else {
        $('#ttr-passwd').removeClass('ttr-pending').removeClass('ttr-valid').addClass('ttr-invalid');
      } 
    } );
  }
}

function goto_summary()
{
  var input = $('#ttr-passwd');
  var passwd = input.val();
  var form = document.createElement('form');
  form.setAttribute('method','post');
  form.setAttribute('action','tt_summary.php');
  var auth = document.createElement('input');
  auth.setAttribute('type','hidden');
  auth.setAttribute('name','ttr_passwd');
  auth.setAttribute('value',passwd);
  form.appendChild(auth);

  document.body.appendChild(form);
  form.submit();
}
