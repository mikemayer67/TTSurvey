$(function() {

  $('#send-id-everyone').on('click',send_id_everyone);

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
      sent = data['sent'];
      failed = data['failed'];
      noemail = data['noemail'];

      result = "<span>"
      + sent.length 
      + " user ID reminders were sent out on: "
      + data['timestamp']
      + "</span>";

      if( failed.length > 0 ) {
        result = result + "<div class='tt-failed'>" + "Email failed for: ";
        for(var i=0; i<failed.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + failed[i];
        }
        result = result + "</div>";
      }
      if( noemail.length > 0) {
        result = result + "<div class='tt-empty'>" + "No email address for: ";
        for(var i=0; i<noemail.length; i++) {
          if( i>0 ) { result = result + ", "; }
          result = result + noemail[i];
        }
        result = result + "</div>";
      }
      $('#send-id-everyone').parent().empty().append(result);
    } )
    .fail( function() {
      alert('Failed to email user ID');
    } );
}
