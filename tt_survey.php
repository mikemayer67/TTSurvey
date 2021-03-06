<!DOCTYPE html>
<html>
<head>
<?php 
require_once(dirname(__FILE__).'/tt_head.php'); 
require_once(dirname(__FILE__).'/db.php');

$user_id = $_SESSION['USER_ID'];
$anon_id  = null; 
$user_info = db_get_user_info($user_id);
$user_name = $user_info['name'];
$user_email = $user_info['email'];

if( isset($_SESSION['ANON_ID']) )
{
  $anon_id = $_SESSION['ANON_ID'];
}

$can_edit = $tt_year == $tt_active_year;

try
{
  db_clone_prior_year($tt_year,$user_id);
?>

<script src="js/tt_survey.js?v=<?=rand()?>"></script>

</head>

<body>

<div id=tt_survey_header class=tt-header>
<span id=tt_user_uid class=tt-user-info>User ID: <span><?=$user_id?></span>
  <button data-role='none'>logout</button></span>

<?php if( $can_edit && db_can_revert($tt_year,$user_id) ) { ?>
<span id=tt_reload class=tt-user-info>Found Submitted Responses: <button data-role=none>Reload</button></span>
<?php } ?>

<span id=tt_user_name class=tt-user-info>Name: <span><?=$user_name?></span>
  <input data-role='none' placeholder='Name...' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
<span id=tt_user_email class=tt-user-info>Email: <span><?=$user_email?></span>
  <input data-role='none' placeholder='(optional)' style='display:none;'></input>
  <button data-role='none'>fix</button></span>
</div>

<div id='tt_landscape_note' data-role='none' style='display:none;'>This page is best viewed in landscape mode on a mobile device</div>

<?=$tt_page_title?>

<form class=tt-survey-form method=post data-ajax=false>
<input type=hidden name=user_id value='<?=$user_id?>'>

<?php
  $groups       = db_get_survey_groups($tt_year);
  $options      = db_get_role_options($tt_year);
  $qualifiers   = db_get_role_qualifiers($tt_year);
  $dependencies = db_get_role_dependencies($tt_year);

  $saved_data = db_retrieve_user_responses($tt_year,$user_id,$anon_id);

  foreach ( $groups as $group )
  {
    $in_list = false;

    $group_id = $group['group_index'];
    $group_label = $group['label'];

    if( $group['collapsible'] )
    {
      $datarole = "data-role='collapsible' data-collapsed='false'";
      $class    = 'tt-group tt-collapsible';
    }
    else
    {
      $datarole = '';
      $class    = 'tt-group tt-non-collapsble';
    }

    print "<div class='$class' $datarole>\n";
    print "<h2 id='survey_group_$group_id' class=tt-group-label>$group_label</h2>\n";

    $items = db_get_survey_items($tt_year, $group_id);

    foreach ( $items as $item )
    {
      $item_id = $item['item_id'];
      $label = $item['label'];
      $anon  = $item['anonymous'];

      switch( $item['item_type'] )
      {
      case 'label':
        $label = $item['label'];
        $type  = $item['type'];
        $size  = $item['size'];

        $list_item = ($type === 'list');
        if(   $list_item && ! $in_list ) { print "<ul>\n";  }
        if( ! $list_item &&   $in_list ) { print "</ul>\n"; }
        $in_list = $list_item;

        switch( $type )
        {
        case 'list':
          $class = label_class($item);
          print "<li class='$class'>$label</li>\n";
          break;
        case 'text':
          $class = label_class($item);
          print "<p class='$class'>$label</p>\n";
          break;
        case 'image':
          print "<img src='img/$label'";
          if( $size ) print " style='height:".$size."px;'";
          print "></img>\n";
          break;
        }

        break;

      case 'role':

        if( $in_list ) { print "</ul>\n"; $in_list=false; }

        $has_qual = false;

        if( isset($options[$item_id]) )
        {
          $item_options = $options[$item_id];

          if( ! isset($item_options['primary']) ) {
            throw new Exception("Missing primary options for item $item_id",500);
          }

          print "<div class='tt-role-box'>";
          print "<div class='tt-role-label'><span>$label</span></div>\n";
          print "<div class='tt-role-options'>\n";

          foreach ( array('primary','secondary') as $key )
          {
            if( isset($item_options[$key]) )
            {
              $opts = $item_options[$key];
              print "<div class='tt-role-$key-options'>\n";
              foreach ( $opts as $opt )
              {
                $opt_id    = $opt['id'];
                $opt_tag   = "item_$opt_id";
                $opt_label = $opt['label'];

                print "<span class='tt-role-option'>";
                print "<input id='$opt_tag' type='checkbox' name='$opt_tag' data-role=none";
                if( isset($dependencies[$opt_id]) )
                {
                  $children = $dependencies[$opt_id];
                  print " data-tt-children='$children'";
                }
                if( isset($qualifiers[$opt_id]) )
                {
                  $has_qual = true;
                  $qual_hint = $qualifiers[$opt_id];
                  $qual_tag  = "qual_$item_id";
                  print " data-tt-qual='#$qual_tag'";
                }
                if( isset($saved_data[$opt_tag]) && $saved_data[$opt_tag] ) 
                { 
                  print " checked";
                }
                print ">";
                print "<label class='tt-role-option' for=$opt_tag>$opt_label</label>";
                print "</input></span>\n";
              }
              print "</div>\n"; // tt-role-(key)-options
            }
          }
          print "</div>\n"; // tt-role-options

          if( $has_qual )
          {
            print "<div class=tt-qualification-text>";
            print "<textarea id='$qual_tag' class='tt-qualtext' name='$qual_tag' placeholder='$qual_hint'>";
            if( isset($saved_data[$qual_tag]) )
            {
              print $saved_data[$qual_tag];
            }
            print "</textarea>";
            print "</div>";
          }

          print "</div>\n"; // tt-role-box
        }
        else  // single unlabeled option
        {
          $tag = "item_$item_id";
          print "<div class='tt-role-bool'>";
          print "<span class='tt-role-bool'>";
          print "<input id='$tag' type='checkbox' name='$tag' data-role=none class='tt-role-bool'";
          if( isset($saved_data[$tag]) && $saved_data[$tag] ) { print " checked"; }
          print ">";
          print "<label class='tt-role-bool' for='$tag'>$label</label>";
          print "</input></span></div>\n";
        }

        break;

      case 'free_text':

        if( $in_list ) { print "</ul>\n"; $in_list=false; }

        $tag = "freetext_$item_id";

        print "<div class=tt-free-text-box'>";
        print "<div><span class='tt-free-text-label'>$label</span>\n";
        if( $anon )
        {
          $anon_tag = "anon_$tag";
          print "<span class=tt-free-text-anon>";
          print "<input id='$anon_tag' type='checkbox' name='$anon_tag' data-role=none";
          if( isset($saved_data[$anon_tag]) && $saved_data[$anon_tag] )
          {
            print " checked";
          }
          print ">anonymous</input>";
          print "</span>\n";
        }
        print "</div>\n";
        print "<textarea id='$tag' class='tt-free-text' name='$tag'>\n";
        if( isset($saved_data[$tag]) )
        {
          print $saved_data[$tag];
        }
        print "</textarea>";
        print "</div>\n";

        break;
      }
    }
    if( $in_list ) { print "</ul>\n"; $in_list=false; }


    if( $group['comment'] )
    {
      $type = $group['comment'];
      $qualifier = $group['comment_label'];

      $label = 'Comments';
      if( isset($tt_comments_label) ) { $label = $tt_comments_label; }

      if( isset($qualifier) )
      {
        if( $type == 1 ) // comment with qualifier
        {
          $label = "$label<span class='tt-comment-qualifier'> ($qualifier)</span>";
        }
        else // comment with label
        {
          $label = $qualifier;
        }
      }
      $tag = "comment_$group_id";

      print "<div class=tt-comment-box>";
      print "<div><span class='tt-comment-label'>$label:</span>\n";
      print "</div>\n";
      print "<textarea id='$tag' class='tt-comment' name='$tag'>\n";
        if( isset($saved_data[$tag]) )
        {
          print $saved_data[$tag];
        }
      print "</textarea>";
      print "</div>\n";
    }

    print "</div>\n";
  }
}
catch (Exception $e)
{
  $msg  = $e->getMessage();
  $file = $e->getFile();
  $line = $e->getLine();

  error_log("${file}[$line]: $msg");

  $page = $e->getCode() . '.php';
?>
  <script>window.location='<?=$page?>';</script>
<?php
}

if($can_edit) {
?>
<div class='submit'><input id=submit_survey_button' type='submit' data-theme=b name=submit_survey value="Submit Survey"></div>
</form>
<?php } else { ?>
</form>
<script type='text/javascript'>disable_edit();</script>
<?php } ?>

</body>
</html>


<?php

function label_class($x)
{
  $class  = 'tt-survey-label';

  if( isset($x['italic']) && $x['italic'] ) { $class .= ' tt-survey-italic'; }
  if( isset($x['bold'] )  && $x['bold']   ) { $class .= ' tt-survey-bold';   }

  switch( $x['size'] )
  {
  case -3: $class .= ' tt-xx-small'; break;
  case -2: $class .= ' tt-x-small';  break;
  case -1: $class .= ' tt-small';    break;
  case  1: $class .= ' tt-large';    break;
  case  2: $class .= ' tt-x-large';  break;
  case  3: $class .= ' tt-xx-large'; break;
  }

  if( isset($x['level']) )
  {
    $class .= ' tt-level-' . floor(abs($x['level']));
  }

  return $class;
}

?>
