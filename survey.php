<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); 

require_once("$dir/db.php");
$user_uid = $_SESSION['USER_ID'];
$user_info = db_user_info($user_uid);
$user_name = $user_info['name'];
$user_email = $user_info['email'];
?>

<script src="js/survey.js?v=<?=rand()?>"></script>

</head>

<body>

<div id=tt_survey_header class=tt-header>
<span id=tt_user_uid class=tt-user-info>User ID: <span><?=$user_uid?></span>
  <button data-role='none'>logout</button></span>
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

try
{
  $db = db_connect();

  $groups       = db_survey_groups($db,$tt_year);
  $options      = db_role_options($db,$tt_year);
  $qualifiers   = db_role_qualifiers($db,$tt_year);
  $dependencies = db_role_dependencies($db,$tt_year);

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

    $items = db_survey_items($db,$tt_year, $group_id);

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
            print "<textarea id='$qual_tag' class='tt-qualtext' name='$qual_tag' placeholder='$qual_hint'></textarea>";
            print "</div>";
          }

          print "</div>\n"; // tt-role-box
        }
        else  // single unlabeled option
        {
          $tag = "item_$item_id";
          print "<div class='tt-role-bool'>";
          print "<input id='$tag' type='checkbox' name='$tag' data-role=none class='tt-role-bool'>";
          print "<span class='tt-role-bool-label'>$label</span>";
          print "</input>";
          print "</div>\n";
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
          print "<input id='$anon_tag' type='checkbox' name='$anon_tag' data-role=none>anonymous</input>";
          print "</span>\n";
        }
        print "</div>\n";
        print "<textarea id='$tag' class='tt-free-text' name='$tag'>\n";
        print "</textarea>";
        print "</div>\n";

        break;
      }
    }
    if( $in_list ) { print "</ul>\n"; $in_list=false; }


    if( $group['comment'] )
    {
      $label = 'Comments';
      if( isset($group['comment_qualifier']) )
      {
        $label .= ' ('.$group['comment_qualifier'].')';
      }
      $label .= ':';
      $tag = "comment_$group_id";

      print "<div class=tt-comment-box>";
      print "<div><span class='tt-comment-label'>$label</span>\n";
      print "</div>\n";
      print "<textarea id='$tag' class='tt-comment' name='$tag'>\n";
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

  error_log("$file\[$line\]: $msg");

  $page = $e->getCode() . '.php';
?>
  <script>window.location='<?=$page?>';</script>
<?php
}
finally
{
  $db->close();
}

?>

<div class='submit'><input id=submit_survey_button' type='submit' data-theme=b name=submit_survey value="Submit Survey"></div>
</form>

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
