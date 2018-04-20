$(function() {

  $('a.tt-menu-action').on('click',function() {

    action = $(this).attr('data-action');
    target = $(this).attr('data-target');

    if( action === 'print' ) {
      p=1;
      switch( $(this).attr('data-target') ) 
      {
        case 'ttr-role':        p=1; break;
        case 'ttr-participant': p=3; break;
        case 'ttr-free-text'  : p=2; break;
      }
      window.open(window.location.pathname + '?print=' + p);
    }
    else {
      $('#'+target+'-block').collapsible(action);
      $('div.'+target).collapsible(action);
    }
  });

  $('a.tt-menu-goto').on('click',function() {
    block  = $(this).attr('data-block');
    target = $(this).attr('data-target');

    target = $('#' + block + '-' + target);
    $('#'+block+'-block').collapsible('expand');
    target.collapsible('expand');

    $('html, body').animate({
      scrollTop: target.offset().top - $('.tt-menu-back').height()
    }, 1000);
  });
});
