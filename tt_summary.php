<?php

$dir = dirname(__FILE__);

require_once("$dir/tt_init.php");
require_once("$dir/ttr_login.php");

$title = $tt_title;

$data = db_all_results($tt_year);

if(isset($_REQUEST['print']))
{
  $print = $_REQUEST['print'];
  $include_summary_by_ministry_area  = ( $print==1 );
  $include_summary_of_open_responses = ( $print==2 );
  $include_summary_by_participant    = ( $print==3 );
  $include_buttons = false;
  $is_print        = true;
}
else
{
  $include_summary_by_ministry_area  = true;
  $include_summary_of_open_responses = true;
  $include_summary_by_participant    = true;
  $include_buttons = true;
  $is_print        = false;
}

$ui_button = 'ui-btn ui-btn-inline ui-mini'; 

print "<!DOCTYPE html>\n";
print "<html>\n";
print "<head>\n";

require("$dir/tt_head.php");

if($is_print)
{
?>
  <script type='text/javascript'>
  window.onload = function() { setTimeout(function() { window.print(); }, 500); };
  </script>
<?php
}
else
{
  $v = rand();
  print "<link rel='stylesheet' type='text/css' href='tt_menu.css?v=$v'>";
  print "<script src='js/tt_summary.js?v=$v'></script>\n";
}

print "</head>\n";

print "<body class=ttr>\n";

if( ! $is_print )
{
  print "<div id=tt-menu><ul class=tt-menu>\n";
  print "<li><a href='#'>Ministry Areas</a><ul>\n";
  print "<li><a class=tt-menu-action data-action=expand   data-target=ttr-role href='#'>Open All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=collapse data-target=ttr-role href='#'>Close All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=print    data-target=ttr-role href='#'>Print All</a></li>\n";
  print "<li class=tt-menu-sep> </li>\n";
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['roles'] ) ) {
      $label = $group['label'];
      print "<li><a class=tt-menu-goto data-block=ttr-role data-target=$group_id href='#'>$label</a></li>\n";
    }
  }
  print "</ul></li>\n";
  print "<li><a href='#'>Participants</a><ul class=tt-menu>\n";
  print "<li><a class=tt-menu-action data-action=expand   data-target=ttr-participant href='#'>Open All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=collapse data-target=ttr-participant href='#'>Close All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=print    data-target=ttr-participant href='#'>Print All</a></li>\n";
  print "<li class=tt-menu-sep> </li>\n";
  if( isset($data['user_responses']) )
  {
    $names = array_keys($data['user_responses']);
    usort($names,'lastNameSort');

    $num_names = count($names);
    $end_imenu = "";

    foreach ( $names as $name )
    {
      $username = strtolower(preg_replace('/\s/','_',$name));

      if($num_names>15)
      {
        $lastname = explode(' ', $name);
        $last_initial = substr( end($lastname), 0, 1 );

        if(empty($cur_initial) || ($cur_initial != $last_initial) )
        {
          print $end_imenu;
          print "<li><a href='#'>$last_initial &gt;</a><ul>\n";
          $cur_initial = $last_initial;
          $end_imenu = "</ul></li>\n";
        }
      }

      print "<li><a class=tt-menu-goto data-block=ttr-participant data-target='$username' href='#'>$name</a></li>\n";
    }
    print $end_imenu;
  }
  print "</ul></li>\n";
  print "<li><a href='#'>Open Responses</a><ul class=tt-menu>\n";
  print "<li><a class=tt-menu-action data-action=expand   data-target=ttr-free-text href='#'>Open All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=collapse data-target=ttr-free-text href='#'>Close All</a></li>\n";
  print "<li><a class=tt-menu-action data-action=print    data-target=ttr-free-text href='#'>Print All</a></li>\n";
  print "<li class=tt-menu-sep> </li>\n";
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['free_text'] ) ) {
      $label = $group['label'];
      print "<li><a class=tt-menu-goto data-block=ttr-free-text data-target=$group_id href='#'>$label</a></li>\n";
    }
  }
  print "</ul></li>\n";
  print "</ul></div>\n";

  print "<div class=tt-menu-back> </div>\n";
}

print "<h1><img src='img/cts_logo.png' height=50>$tt_title Result Summary</h1>\n";
print "<div data-role=collapsibleset>\n";

if($include_summary_by_ministry_area) 
{
  print "<div id=ttr-role-block data-role=collapsible>\n";
  print "<h2 id=summary_by_roles>Summary by Ministry Area</h2>\n";
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['roles'] ) ) {
      print "<div data-role=collapsibleset>\n";
      print "<div id='ttr-role-$group_id' class=ttr-role data-role=collapsible>\n";
      print "<h3>".$group['label']."</h3>\n";
      foreach ( $group['roles'] as $item_id ) { 
        $role = $data['roles'][$item_id];
        $role_label = $role['label'];
        if( isset( $data['response_summary'][$item_id] ) )
        {
          $responses = $data['response_summary'][$item_id];

          $names = array_keys($responses);
          usort($names,'lastNameSort');

          if( isset($role['options']) )
          {
            print "<div class=ttr-table-block>";
            print "<table class=ttr-role-options data-role=none>\n";
            print "<tr class=ttr-role-options-header data-role=none>\n";
            print "<th class=ttr-role-label>$role_label</th>\n";

            $option_ids = array();
            foreach ( $role['options'] as $opt_id=>$opt_label)
            {
              print "<th class=ttr-role-option-label>$opt_label</th>";
              $option_ids[] = $opt_id;
            }
            print "</tr>\n";
            foreach ( $names as $name )
            {
              $response = $responses[$name];

              print "<tr class=ttr-user-response>\n";
              print "<td class=ttr-username>$name</td>";
              foreach ( $option_ids as $option_id )
              {
                if( isset($response['options'][$option_id]) && $response['options'][$option_id] )
                {
                  print "<td class=ttr-role-cell>x</td>";
                }
                else
                {
                  print "<td class=ttr-role-cell></td>";
                }
              }
              if ( isset($response['qualifier']) )
              {
                $qualifier = $response['qualifier'];
                $qualifier = preg_replace('/¶/u','<br>',$qualifier);
                print "<td class=ttr-qualifier>$qualifier</td>";
              }
              print "</tr>\n";
            }

            print "</table></div>\n";
          }
          else
          {
            print "<div class=ttr-role-label>$role_label</div>\n";
            print "<div class=ttr-usernames>\n";
            foreach ($names as $name)
            {
              $response = $responses[$name];
              print "<span class=ttr-username>$name</span>\n";
            }
            print "</div>\n";
          }
        }
        else
        {
          print "<div class=ttr-table-block>\n";
          print "<div class=ttr-role-label>$role_label</div>\n";
          print "<div class=ttr-no-response>(no responses)</div>\n";
          print "</div>\n";
        }
      }
      
      if( isset($data['comment_summary'][$group_id] ) )
      {
        print "<div class=ttr-role-label>General Comments</div>\n";

        $comments = $data['comment_summary'][$group_id];

        $names = array_keys($comments);
        usort($names,'lastNameSort');

        print "<table class=ttr-comments data-role=none>\n";
        foreach ($names as $name)
        {
          $comment = $comments[$name];
          $comment = preg_replace('/¶/u','<br>',$comment);
          print "<tr class=ttr-user-comment>\n";
          print "<td class=ttr-comment-username>$name</td>";
          print "<td class=ttr-comment>$comment</td>";
          print "</tr>\n";
        }
        print "</table>\n";
      }

      print "</div></div>\n";  // group collapsible, group collapsibleset
    }
  }
  print "</div>\n";
}

if($include_summary_by_participant)
{
  print "<div id=ttr-participant-block data-role=collapsible>\n";
  print "<h2 id=summary_by_participants>Summaries by Participants</h2>\n";

  if( isset($data['user_responses']) )
  {
    $user_responses = $data['user_responses'];
    $names = array_keys($user_responses);
    usort($names,'lastNameSort');

    foreach ($names as $name)
    {
      $responses = $user_responses[$name];
      $username = strtolower(preg_replace('/\s/','_',$name));

      print "<div data-role=collapsibleset>\n";
      print "<div id='ttr-participant-$username' class=ttr-participant data-role=collapsible>\n";
      print "<h3>$name</h3>\n";

      foreach ($responses as $group_index=>$group_responses)
      {
        $group = $data['groups'][$group_index];
        $group_label = $group['label'];

        $has_roles   = isset($group_responses['roles']);
        $has_comment = isset($group_responses['comment']);

        if( $has_roles || $has_comment )
        {
          print "<div class=ttr-table-block>";
          print "<table class=ttr-user-group data-role=none>\n";
          print "<tr class=ttr-user-group-header data-role=none>\n";
          print "<th class=ttr-user-group-label colspan=3>$group_label</th>\n";

          if($has_roles)
          {
            $roles = $group_responses['roles'];

            foreach ($roles as $item_id=>$role)
            {
              $role_label = $data['roles'][$item_id]['label'];
              print "<tr class=ttr-user-response>\n";

              $has_qualifier = isset($group_responses['qualifiers'][$item_id]);

              if( isset($role['options'] ) )
              {
                $options = array();
                foreach ( $role['options'] as $option_id=>$selected )
                {
                  if($selected)
                  {
                    $options[] = $data['roles'][$item_id]['options'][$option_id];
                  }
                }
                $options = implode(', ', $options);
                $colspan = ($has_qualifier ? 1 : 2);
                print "<td class=ttr-user-response>$role_label</td>";
                print "<td class=ttr-user-option colspan=$colspan>$options</td>";
              }
              else
              {
                $colspan = ($has_qualifier ? 2 : 3);
                print "<td class=ttr-user-response colspan=$colspan>$role_label</td>";
              }
              if( $has_qualifier )
              {
                $qualifier = $group_responses['qualifiers'][$item_id];
                print "<td class=ttr-user-qualifier>$qualifier</td></tr>\n";
              }
            }
          }
          if($has_comment)
          {
            $comment = $group_responses['comment'];
            print "<tr class=ttr-user-responses>\n";
            print "<td class=ttr-user-comment colspan=3>$comment</td></tr>\n";
          }
          print "</table></div>\n";
        }
      }
      print "</div></div>\n";
    }
  }
  print "</div>\n";
}

if($include_summary_of_open_responses) 
{
  print "<div id=ttr-free-text-block data-role=collapsible>\n";
  print "<h2 id=summary_by_free_text>Summary of Open Responses by Ministry Area</h2>\n";
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['free_text'] ) ) {
      print "<div data-role=collapsibleset>\n";
      print "<div id='ttr-free-text-$group_id' class=ttr-free-text data-role=collapsible>\n";
      print "<h3>".$group['label']."</h3>\n";
      foreach ( $group['free_text'] as $item_id ) { 
        $free_text_label = $data['free_text'][$item_id];
        print "<div class=ttr-role-label>$free_text_label</div>\n";

        $has_names = ! empty($data['response_summary']);
        $has_anon  = ! empty($data['anonymous_summary']);
        if( $has_names || $has_anon )
        {
          print "<table class=ttr-comments data-role=none>\n";
          if($has_names && isset($data['response_summary'][$item_id]))
          {
            $responses = $data['response_summary'][$item_id];

            $names = array_keys($responses);
            usort($names,'lastNameSort');

            foreach ($names as $name)
            {
              $response = $responses[$name];
              $response = preg_replace('/¶/u','<br>',$response);
              print "<tr class=ttr-user-comment>\n";
              print "<td class=ttr-comment-username>$name</td>";
              print "<td class=ttr-comment>$response</td>";
              print "</tr>\n";
            }
          }
          if($has_anon and isset($data['anonymous_summary'][$item_id]))
          {
            $responses = $data['anonymous_summary'][$item_id];

            foreach ($responses  as $response)
            {
              $response = preg_replace('/¶/u','<br>',$response);
              print "<tr class=ttr-user-comment>\n";
              print "<td/><td class=ttr-comment>$response</td>";
              print "</tr>\n";
            }
          }
          print "</table>\n";
        }
        else
        {
          print "<div class=ttr-no-response>(no responses)</div>\n";
        }
      }
      print "</div></div>\n";  // group collapsible, group collapsibleset
    }
  }
  print "</div>\n";
}

print "</div>\n";
print "</div>\n";
print "</body>\n";
print "</html>\n";

function lastNameSort($a,$b)
{
  $aa = explode(' ', $a);
  $bb = explode(' ', $b);

  $aLast = end($aa);
  $bLast = end($bb);

  return strcasecmp($aLast, $bLast);
}

