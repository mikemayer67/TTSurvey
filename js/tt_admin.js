$(function() {

  $('#send-id-everyone').on('click',send_id_everyone);
  $('button.tta-send-id').on('click',tta_send_userid);
  $('button.tta-fix-name').on('click',tta_fix_name);
  $('button.tta-fix-email').on('click',tta_fix_email);

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
  alert('fix_name: '+id);
}

function tta_fix_email()
{
  alert('fix_email: '+id);
}
