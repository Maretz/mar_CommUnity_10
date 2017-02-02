<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: Regeln';
$hmenu = 'Regeln';
$design = new design ( $title , $hmenu );
$design->header();





//-----------------------------------------------------------|


  $erg = db_query('SELECT zahl,titel,text FROM `prefix_rules` ORDER BY zahl');
  echo '<div class="com10newsinput">';
	while ($row = db_fetch_row($erg)) {
			echo '<div class="panel panel-default">';
		  echo '<div class="panel-heading"><strong><i class="fa fa-paragraph" aria-hidden="true"></i> '.$row[0].'.</strong>&nbsp; '.$row[1].'</div>';
			echo '<div class="panel-body">'.bbcode($row[2]).'</div>'; 
			echo '</div>';
  } 
echo '</div>';

$design->footer();

?>