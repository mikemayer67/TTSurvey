<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title class=tt-title><?=$tt_title?></title>

<!-- Include meta tag to ensure proper rendering and touch zooming -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php 
  $v=rand();
  if(isset($_REQUEST['print'])) { return; } 
?>

<!-- Include jQuery Mobile stylesheets -->
<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

<!-- Include the jQuery library -->
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Include the jQuery Mobile library -->

  <script>
  $(document).on("mobileinit", function(){
    $.extend( $.mobile, {
      linkBindingEnabled: false,
        ajaxEnabled: false
} );
});

</script>

<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

<!-- T&T Survey stylesheet -->
  <link rel='stylesheet' type='text/css' href='tt_style.css?v=<?=$v?>'>
