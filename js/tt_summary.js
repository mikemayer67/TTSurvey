$(function() {

  $('.tt-open-all.tt-roles').on('click',function() {
    $('div.tt-role').collapsible('expand');
  });

  $('.tt-close-all.tt-roles').on('click',function() {
    $('div.tt-role').collapsible('collapse');
  });

  $('.tt-print-all.tt-roles').on('click',function() {
    window.open(window.location.pathname + '?print=1');
  });

  $('.tt-open-all.tt-free-text').on('click',function() {
    $('div.tt-free-text').collapsible('expand');
  });

  $('.tt-close-all.tt-free-text').on('click',function() {
    $('div.tt-free-text').collapsible('collapse');
  });

  $('.tt-print-all.tt-free-text').on('click',function() {
    window.open(window.location.pathname + '?print=2');
  });

  $('.tt-open-all.tt-participants').on('click',function() {
    $('div.tt-participant').collapsible('expand');
  });

  $('.tt-close-all.tt-participants').on('click',function() {
    $('div.tt-participant').collapsible('collapse');
  });

  $('.tt-print-all.tt-participants').on('click',function() {
    window.open(window.location.pathname + '?print=3');
  });

});
