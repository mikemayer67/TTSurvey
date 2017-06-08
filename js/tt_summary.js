$(function() {

  $('.tt-open-all-groups').on('click',function() {
    $('div.tt-collapsible-group').collapsible('expand');
  });

  $('.tt-close-all-groups').on('click',function() {
    $('div.tt-collapsible-group').collapsible('collapse');
  });

});
