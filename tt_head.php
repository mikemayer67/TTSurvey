<?php if($tt_nojs) {?>
<script>
window.location.replace('tt.php');
</script>
<?php } ?>

<meta charset="UTF-8">
<title><?=$tt_title?></title>

<!-- Include meta tag to ensure proper rendering and touch zooming -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Include jQuery Mobile stylesheets -->
<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

<!-- Include the jQuery library -->
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Include the jQuery Mobile library -->
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

<!-- T&T Survey stylesheet -->
<link rel='stylesheet' type='text/css' href="tt_style.css?v=<?=rand()?>">
