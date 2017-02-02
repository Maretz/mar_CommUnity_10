<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );




//-----------------------------------------------------------|


##
###
####
##### ins vote
$um = $menu->get(1);
if ($menu->getA(1) == 'W') {

  $poll_id = escape ($menu->getE(1), 'integer');
	$radio = escape ($_POST['radio'], 'integer');
	
		$fraRow = db_fetch_object(db_query("SELECT * FROM prefix_poll WHERE poll_id = '".$poll_id."'"));
	  $textAr = explode('#',$fraRow->text);
	  if ($fraRow->recht == 2) {
		  $inTextAr = $_SESSION['authid'];
		} elseif ($fraRow->recht == 1) {
		  $inTextAr = $_SERVER['REMOTE_ADDR'];
		}
		if ( !in_array ( $inTextAr , $textAr ) ) {
			$textAr[] = $inTextAr;
		  $textArString = implode('#',$textAr);
      db_query('UPDATE `prefix_poll` SET text = "'.$textArString.'" WHERE poll_id = "'.$poll_id.'"');
		  db_query('UPDATE `prefix_poll_res` SET res = res + 1 WHERE poll_id = "'.$poll_id.'" AND sort = "'.$radio.'" LIMIT 1') or die (db_error());
		}
		
}

##
###
####
##### V o t e    Ü b e r s i c h t 

$title = $allgAr['title'].' :: '.$lang['vote'];
$hmenu = $lang['vote'];
$design = new design ( $title , $hmenu );
$design->header();

?>
<table width="100%" class="table  table-bordered">
  <tr class="active">
    <td><b><?php $lang['vote']; ?></b></td>
  </tr>
	
<?php

$breite = 200;
if ($_SESSION['authright'] <= -1 ) {
	  $woR = '>= "1"';
} else {
	  $woR = '= "1"';
}
$limit = 3;  // Limit 
$page = ( $menu->getA(1) == 'p' ? $menu->getE(1) : 1 );
$MPL = db_make_sites ($page , 'WHERE recht '.$woR , $limit , "?vote" , 'poll' );
$anfang = ($page - 1) * $limit;
$class = '';
$erg = db_query('SELECT * FROM `prefix_poll` WHERE recht '.$woR.' ORDER BY poll_id DESC LIMIT '.$anfang.','.$limit);
while ($fraRow = db_fetch_object($erg)) {

	$maxRow = db_fetch_object(db_query('SELECT MAX(res) as res FROM `prefix_poll_res` WHERE poll_id = "'.$fraRow->poll_id.'"'));
	$gesRow = db_fetch_object(db_query('SELECT SUM(res) as res FROM `prefix_poll_res` WHERE poll_id = "'.$fraRow->poll_id.'"'));
	$max = $maxRow->res;
  $ges = $gesRow->res;
	$textAr = explode('#',$fraRow->text);
	
	  if ($fraRow->recht == 2) {
		  $inTextAr = $_SESSION['authid'];
		} elseif ($fraRow->recht == 1) {
		  $inTextAr = $_SERVER['REMOTE_ADDR'];
		}
    echo '<tr><td><b>'.$fraRow->frage.'</b></td></tr>';
		if ( $class == 'Cnorm' ) { $class = 'Cmite'; } else { $class = 'Cnorm'; }
		echo '<tr><td class="'.$class.'">';
		if ( in_array ( $inTextAr , $textAr ) OR $fraRow->stat == 0) {
			  echo '<table width="100%" class="table">';
		    $imPollArrayDrin = true;
		} else {
			  echo '<form action="index.php?vote-W'.$fraRow->poll_id.'" method="POST" role="form">';
		    $imPollArrayDrin = false;
		}
    $i = 0;
		$pollErg = db_query('SELECT antw, res, sort FROM `prefix_poll_res` WHERE poll_id = "'.$fraRow->poll_id.'" ORDER BY sort');
		while ( $pollRow = db_fetch_object($pollErg) ) {
		    if ( $imPollArrayDrin ) {
	 		     if ( !empty($pollRow->res) ) {  
				      $weite = ($pollRow->res / $max) * 100;
		 		      $prozent = $pollRow->res * 100 / $ges;
		 		      $prozent = round($prozent,0);
				    } else {
		  		    $weite = 0;
					    $prozent = 0;
				    }
						$tbweite = $weite + 20;
						echo '<tr><td>'.$pollRow->antw.'<br>';
				    echo '';
            /*
            '<table width="'.$tbweite.'" border="0" cellpadding="0" cellspacing="0"></td>';
						echo '<tr><td width="10" height="10"></td>';
						echo '<td width="'.$weite.'" background="include/images/vote/voteMitte.jpg" alt=""></td>';
						echo '<td width="10"><img src="include/images/vote/voteRight.jpg" alt=""></td>';
						echo '</tr></table>';*/
            echo '
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="'.$weite.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$weite.'%">'.$prozent.'%</div>
</div>';			    
				    echo '<td align="right"><span class="badge">'.$pollRow->res.'</span></td></tr>';
				} else {
            $i++;
			      echo '<div class="radio"><label><input type="radio" id="vote'.$i.'" name="radio" value="'.$pollRow->sort.'">'.$pollRow->antw.'</label></div>';
		    }
		} 
		if ( $imPollArrayDrin ) {
			  echo '<tr><td colspan="2" align="right"><ul class="nav nav-pills nav-stacked">
  <li class="active">
    <a href="#">
      <span class="badge pull-right">'.$ges.'</span>
      '.$lang['whole'].'
    </a>
  </li>
</ul></td></tr></table>';
		} else {
		    echo '<p class="text-center"><input type="submit" class="btn btn-primary" value="'.$lang['formsub'].'"></p></form>';
		}
		
    echo '</td></tr>';
		
} // end while

echo '<tr><td align="center">'. $MPL .'</td></tr></table>';
$design->footer();

?>

