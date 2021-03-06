<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

  $title = $allgAr['title'].' :: Statistik';
  $hmenu = 'Statistik';
  $design = new design ( $title , $hmenu );
  $design->header();

	$anzahlShownTage = 7;
	
	echo '<table width=90%" class="table"><tr><td>';
  echo '<table width="100%" class="table">';
  echo '<tr class="active"><td colspan="3" align="center"><b>Site Statistic</b></td></tr>';
	
	$max_in = 0;
	$ges = 0;
	$dat = array();
	$max_width = 200;
	
	$maxErg = db_query('SELECT MAX(count) FROM `prefix_counter`');
	$max_in = db_result($maxErg,0);
	
	$erg = db_query ("SELECT count, DATE_FORMAT(date,'%a der %d. %b') as datum FROM `prefix_counter` ORDER BY date DESC LIMIT ".$anzahlShownTage);
	while ($row = db_fetch_row($erg) ) {
	
	  $value = $row[0];

		if ( empty($value) ) {
		  $bwidth = 0;
	  } else {
		  $bwidth = $value/$max_in * $max_width;
		  $bwidth = round($bwidth,0);
		}  
		
		echo '<tr>';
	  echo '<td width="50%">'.$row[1].'</td>';
		echo '<td align="center"><table width="'.$bwidth.'" class="table table-bordered">';
		echo '<tr><td height="2" class="active"></td></tr></table>';			
		echo '</td><td align="right"><span class="badge">'.$value.'</span></td></tr>';
	  
		$ges += $value;
	}
	
	$gesBesucher = db_query('SELECT SUM(count) FROM prefix_counter');
	$gesBesucher = @db_result($gesBesucher,0);
	
	echo '<tr><td colspan="3">';
	echo $lang['weeksum'].'&nbsp;&nbsp;<span class="badge">'.$ges.'</span><br>';
	echo $lang['wholevisitor'].'&nbsp;&nbsp;<span class="badge">'.$gesBesucher.'</span><br>'.$lang['max'].'&nbsp;&nbsp;<span class="badge">'.$max_in.'</span><br><br>';
	echo '</td></tr></table></td></tr></table><br />';
  
$design->footer();
?>

