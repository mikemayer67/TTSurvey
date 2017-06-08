<?php

$dir = dirname(__FILE__);
require_once("$dir/tt_init.php");

$title = $tt_title;

$db = db_connect();

$data = db_all_results($db,$tt_year);

?>
<!DOCTYPE html>
<html>
<head>
<?php require("$dir/tt_head.php"); ?>

<script src="js/tt_summary.js?v=<?=rand()?>"></script>

</head>

<body>
<h1><img src='img/cts_logo.png' height=50><?=$tt_title?> Result Summary</h1>

<div data-role=collapsibleset>
  <div data-role=collapsible>
    <h2 id=summary_by_group>Summary by Ministry Area</h2>
    <div class=ttr-buttons>
      <button class='ui-btn ui-btn-inline ui-mini tt-close-all-groups'>Close all Ministry Areas</button>
      <button class='ui-btn ui-btn-inline ui-mini tt-open-all-groups'>Open all Ministry Areas</button>
    </div>
<?php
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['roles'] ) ) {
      print "<div data-role=collapsibleset>\n";
      print "<div class=tt-collapsible-group data-role=collapsible>\n";
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
?>
    <div class=ttr-buttons>
      <button class='ui-btn ui-btn-inline ui-mini tt-close-all-groups'>Close all Ministry Areas</button>
      <button class='ui-btn ui-btn-inline ui-mini tt-open-all-groups'>Open all Ministry Areas</button>
    </div>

  </div>

  <div data-role=collapsible>
    <h2 id=summary_by_group>Summary of Open Responses by Worship Area</h2>
    <div class=ttr-buttons>
      <button class='ui-btn ui-btn-inline ui-mini tt-close-all-groups'>Close all Ministry Areas</button>
      <button class='ui-btn ui-btn-inline ui-mini tt-open-all-groups'>Open all Ministry Areas</button>
    </div>
<?php
  foreach ( $data['groups'] as $group_id => $group ) {
    if( isset( $group['free_text'] ) ) {
      print "<div data-role=collapsibleset>\n";
      print "<div class=tt-collapsible-group data-role=collapsible>\n";
      print "<h3>".$group['label']."</h3>\n";
      foreach ( $group['free_text'] as $item_id ) { 
        $free_text_label = $data['free_text'][$item_id];
        $has_names = isset($data['response_summary']);
        $has_anon  = isset($data['anonymous_summary']);
        if( $has_names || $has_anon )
        {
          print "<table class=ttr-comments data-role=none>\n";
          if($has_names)
          {
            $responses = $data['response_summary'][$item_id];

            $names = array_keys($responses);
            usort($names,'lastNameSort');

            foreach ($names as $name)
            {
              $response = $responses[$name];
              print "<tr class=ttr-user-comment>\n";
              print "<td class=ttr-comment-username>$name</td>";
              print "<td class=ttr-comment>$response</td>";
              print "</tr>\n";
            }
          }
          if($has_anon)
          {
            $responses = $data['anonymous_summary'][$item_id];

            foreach ($responses  as $response)
            {
              print "<tr class=ttr-user-comment>\n";
              print "<td/><td class=ttr-comment>$response</td>";
              print "</tr>\n";
            }
          }
          print "</table>\n";
        }
        else
        {
          print "<div class=ttr-role-label>$free_text_label</div>\n";
          print "<div class=ttr-no-response>(no responses)</div>\n";
        }
      }
      print "</div></div>\n";  // group collapsible, group collapsibleset
    }
  }
?>
    <div class=ttr-buttons>
      <button class='ui-btn ui-btn-inline ui-mini tt-close-all-groups'>Close all Ministry Areas</button>
      <button class='ui-btn ui-btn-inline ui-mini tt-open-all-groups'>Open all Ministry Areas</button>
    </div>
  </div>

  <div data-role=collapsible>
    <h2 id=summary_by_participants>Summaries by Participants</h2>
  </div>
</div>

</body>
</html>

<?php
function lastNameSort($a,$b)
{
  $aLast = end(explode(' ', $a));
  $bLast = end(explode(' ', $b));

  return strcasecmp($aLast, $bLast);
}
?>
