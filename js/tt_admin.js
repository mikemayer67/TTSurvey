$(function() {

  $('#send-id-everyone').on('click',send_id_everyone);
  $('button.tta-send-id').on('click',tta_send_userid);
  $('button.tta-fix-name').on('click',tta_fix_name);
  $('button.tta-fix-email').on('click',tta_fix_email);

  $('#tta-fix-email-popup button.tta-cancel').on('click', function() { 
    $('#tta-fix-email-popup').popup("close"); 
  } );

  $('#tta-fix-email-popup button.tta-ok').on('click', tta_fix_user_email);
});

function send_id_everyone()
{
  year = $('#send-id-everyone').data('year');

  $.ajax( {
    type: 'POST',
    url: "ajax_send_all_userids.php",
    data: { year: year },
  } )
    .done( function(data) {
      sent    = data['sent'];
      failed  = data['failed'];
      noemail = data['noemail'];
      toosoon = data['toosoon'];

      result = '';
      if( sent.length > 0 ) {
        result = result + "<div class='tta-email-sent'>Email sent to " 
          + sent.length + " recipient" + (sent.length>1?'s':'') + ": ";
        for(var i=0; i<sent.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + sent[i];
        }
        result = result + "</div>";
      }
      if( toosoon.length > 0 ) {
        result = result 
          + "<div class='tta-too-soon'>Too soon to send another email to " 
          + toosoon.length + " recipient" + (toosoon.length>1?'s':'') + ": ";
        for(var i=0; i<toosoon.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + toosoon[i];
        }
        result = result + "</div>";
      }
      if( noemail.length > 0) {
        result = result + "<div class='tta-no-email-address'>No email address for "
          + noemail.length + " recipient" + (noemail.length>1?'s':'') + ": ";
        for(var i=0; i<noemail.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + noemail[i];
        }
        result = result + "</div>";
      }
      if( failed.length > 0 ) {
        result = result + "<div class='tta-email-failed'>Email failed for "
          + failed.length + " recipient" + (failed.length>1?'s':'') + ": ";
        for(var i=0; i<failed.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + failed[i];
        }
        result = result + "</div>";
      }
      $('#send-id-everyone').parent().empty().append(result);

      sentids = data['sentids'];
      if( sentids.length > 0 ) {
        last_sent = 'sent ' + data['time'];
        for( var i=0; i<sentids.length; i++ ) {
          id = sentids[i];
          key = "tr.tta-userid[data-id='" + sentids[i] + "']";
          $(key + ' .tta-nosend-rationale').empty().append(last_sent);
          $(key + ' .tta-send-id').attr('disabled',true);
        }
      }

    } )
    .fail( function(jqXHR, textStatus, errorCode) {
      alert('Failed to email user ID [' + errorCode + ']: ' + textStatus);
    } );
}

function tta_send_userid()
{
  userid = $(this).closest('tr').data('id');

  $.ajax( {
    type: 'POST',
    url: "ajax_send_userid.php",
    data: { userid: userid },
  } )
    .done( function(data) {
      key = "tr.tta-userid[data-id='" + userid + "']";
      $(key + ' .tta-nosend-rationale').empty().append('sent ' + data['time']);
      $(key + ' .tta-send-id').attr('disabled',true);

    } )
    .fail( function(jqXHR, textStatus, errorCode) {
      alert('Failed to email user ID [' + errorCode + ']: ' + textStatus);
    } );
}

function tta_fix_name()
{
  userid = $(this).closest('tr').data('id');
  alert('fix_name: '+userid);
}

function tta_fix_email()
{
  tr = $(this).closest('tr');
  user_id = tr.data('id');
  user_name = tr.data('name');
  user_email = tr.data('email');
  $('#tta-fix-email-name').empty().append(user_name);
  $('#tta-fix-email-new').val(user_email);
  $('#tta-fix-email-popup').popup("option",'dismissible',false).popup('open');

  $('#tta-fix-email-new').data('id',user_id).data('name',user_name).data('old_email',user_email);

  tta_validate_user_email();
  $('#tta-fix-email-new').on('input',tta_validate_user_email);
}

function tta_validate_user_email()
{
  var valid   = /^\s*([\w-]*\w\.)*[\w-]*\w@(\w+\.)+(com|net|gov|edu)\s*$/;
  var partial = /^\s*[\w.-]*@*[\w.]*\s*$/;

  var input   = $('#tta-fix-email-new');
  var email   = input.val();

  var old_email = $('#tta-fix-email-new').data('old_email');

  email_is_good = valid.test(email);

  ok = $('#tta-fix-email-popup button.tta-ok');
  if(email_is_good) {
    tta_set_state(input,'tta-valid');
    ok.prop('disabled',email===old_email);
  } else {
    ok.prop('disabled',true);

    if( partial.test(email) ) {
      tta_set_state(input,'tta-pending');
    } else {
      tta_set_state(input,'tta-invalid');
    }
  }
}

function tta_fix_user_email()
{
  var input = $('#tta-fix-email-new');
  var email = input.val();
  var id    = input.data('id');
  var name  = input.data('name');
  
  $.ajax( {
    type: 'POST',
    url:  'ajax_fix_user_email.php',
    data: { user_id: id, user_email: email },
  } )
    .done( function() {
      tr  = $("tr[data-id='"+id+"']");
      tde = tr.find('td.tta-email').find('span').text(email)
      tr.data('email',email);
    } )
    .fail( function() {
      alert('Failed to update email for '+name+'('+id+')');
    } )
    .always( function() {
      $('#tta-fix-email-popup').popup("close"); 
    } );
}

function tta_set_state(item,state)
{
  item.removeClass('tta-valid').removeClass('tta-invalid').removeClass('tta-pending').addClass(state);
}

