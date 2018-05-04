// Note that this uses the same css file (ttr.ccs) as the summary page
// While all IDs and variable names use the 'tta' prefix,
//   css classes use the 'ttr' prefix

$(function() {
  validate_passwd();

  $('#tta-passwd').on('input',validate_passwd);
  $('#tta-login-submit').on('click',goto_summary);
});

function validate_passwd()
{
  var input = $('#tta-passwd');
  var passwd = input.val();

  if(passwd.length == 0) {
    $('#tta-passwd').removeClass('ttr-invalid').removeClass('ttr-valid').addClass('ttr-pending');
    $('#tta-login-submit').hide();
  }
  else {
    $('#tta-login-submit').hide();
    $.ajax( {
      type: 'POST',
      url: 'ajax/validate_tta_login.php',
      data: { value: passwd },
    })
    .done( function(data) {
      if( data['valid'] ) {
        $('#tta-passwd').removeClass('ttr-pending').removeClass('ttr-invalid').addClass('ttr-valid');
        $('#tta-login-submit').show();
      }
      else {
        $('#tta-passwd').removeClass('ttr-pending').removeClass('ttr-valid').addClass('ttr-invalid');
      } 
    } );
  }
}

function goto_summary()
{
  var input = $('#tta-passwd');
  var passwd = input.val();
  var form = document.createElement('form');
  form.setAttribute('method','post');
  form.setAttribute('action','tt_admin.php');
  var auth = document.createElement('input');
  auth.setAttribute('type','hidden');
  auth.setAttribute('name','tta_passwd');
  auth.setAttribute('value',passwd);
  form.appendChild(auth);

  document.body.appendChild(form);
  form.submit();
}
