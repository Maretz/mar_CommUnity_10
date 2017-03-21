<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
setlocale(LC_TIME, "de_DE");
# check ob ein fehler aufgetreten ist.
check_forum_failure($forum_failure);

# toipc als gelesen markieren
$_SESSION['forumSEE'][$fid][$tid] = time();

$title = $allgAr['title'].' :: Forum :: '.$aktTopicRow['name'].' :: Beitr&auml;ge zeigen';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= $aktTopicRow['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();
    
# Topic Hits werden eins hochgesetzt.
db_query('UPDATE `prefix_topics` SET hit = hit + 1 WHERE id = "'.$tid.'"');

# mehrere seiten fals gefordert
$limit = $allgAr['Fpanz'];  // Limit
$page = ($menu->getA(3) == 'p' ? $menu->getE(3) : 1 );
$MPL = db_make_sites ($page , "WHERE tid = ".$tid , $limit , 'index.php?forum-showposts-'.$tid , 'posts' );
$anfang = ($page - 1) * $limit;

$antworten = '';
if (($aktTopicRow['stat'] == 1 AND $forum_rights['reply'] == TRUE) OR ($_SESSION['authright'] <= '-7' OR $forum_rights['mods'] == TRUE)) {
  $antworten = '<a class="btn btn-primary btn-sm" href="index.php?forum-newpost-'.$tid.'">'.$lang['answer'].'</a>';
} 

# News Teilen Funktion URL
function url($teilenurl){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
}

$class = '';
$postreply = db_result(db_query("SELECT COUNT(tid) FROM prefix_posts WHERE tid LIKE '$tid%' ORDER by tid DESC"), 0) - 1;
$posthits = db_result(db_query("SELECT hit FROM prefix_topics WHERE id='$tid%'"), 0);
$usernumb = db_result(db_query("SELECT COUNT(DISTINCT erst) FROM prefix_posts WHERE tid LIKE '$tid%' ORDER by erst DESC"), 0);
if ($postreply == 1) {
$postreplyfont = 'Antwort';
} else {
$postreplyfont = 'Antworten';
}
$tpl = new tpl ( 'forum/showpost' );
$ar = array (
  'SITELINK' => $MPL,
  'tid' => $tid,
	'ANTWORTEN' => $antworten,
	'TOPICNAME' => $aktTopicRow['name'],
	'USERNAME' => $aktTopicRow['erst'],
	'USERNUMB' => $usernumb,
	'POSTREPLY' => $postreply,
	'POSTHITS' => $posthits,
	'POSTREPLYFONT' => $postreplyfont
		
	);
$tpl->set_ar_out($ar,0);
$i = $anfang +1;
$ges_ar = array ('wurstegal', 'maennlich', 'weiblich');
$erg = db_query("SELECT geschlecht, prefix_posts.id,txt,time,erstid,erst,sig,avatar,posts FROM `prefix_posts` LEFT JOIN prefix_user ON prefix_posts.erstid = prefix_user.id WHERE tid = ".$tid." ORDER BY time LIMIT ".$anfang.",".$limit);
while($row = db_fetch_assoc($erg)) {

	$class = ( $class == '' ? 'timeline-inverted' : '' );
	
	# define some vars.
	$row['sig'] = ( empty($row['sig']) ? '' : '<br><br><div class="well well-sm wellsmnews">'.bbcode($row['sig']).'</div>' );
	$row['TID'] = $tid;
	$row['class'] = $class;

    $times = $row['time'];
$diff = time() - $row['time']; 
$fullHours = intval($diff/60/60); 
$Minutes = intval(($diff/60)-(60*$fullHours));
if ($Minutes == 0) {
$Minutes = 'gerade eben';
} elseif ($Minutes == 1) {
$Minutes = 'vor einer Minute';
} else {
$Minutes = 'vor '. $Minutes .' Minuten';
}
$zeitposts = strftime("(%H:%M Uhr)", $times);
if ($fullHours == 0) {
$Stunde = $Minutes;
} elseif ($fullHours == 1) {
$Stunde = 'vor einer Stunde '. $zeitposts;
} else {
$Stunde = 'vor '. $fullHours .' Stunden '. $zeitposts;
}

 
$wochentag = strftime("%A", $times); 

        if (date("d.m.Y", $times) == date("d.m.Y")) {
            if ($fullHours < 12) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = strftime("Heute, %H:%M Uhr", $times);
            }
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 24)) {
            if ($fullHours < 12) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = strftime("Gestern, %H:%M Uhr", $times);
            }
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 48)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 72)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 96)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 120)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } else {
            $row['date'] = strftime("%d. %B %Y - %H:%M Uhr", $times);
        }

	$row['delete'] = '';
	$row['change'] = '';

	if (!is_numeric($row['geschlecht'])) { $row['geschlecht'] = 0; }
	if (file_exists($row['avatar'])) { $row['avatar'] = '<img  src="'.$row['avatar'].'" alt="User Pic" border="0" />'; }
	elseif ($allgAr['forum_default_avatar']) { $row['avatar'] = '<img src="include/images/avatars/'.$ges_ar[$row['geschlecht']].'.jpg" alt="User Pic" border="0" />'; }
 	else { $row['avatar'] = ''; }

 	
    $row['rang']   = userrang ($row['posts'],$row['erstid']);
	$row['txt']    = (isset($_GET['such']) ? markword(bbcode ($row['txt']),$_GET['such']) : bbcode ($row['txt']) );
	$row['i']      = $i;
    $row['page']   = $page;


# forum Teilen Start
$row['teilenurl'] = url($row['teilenurl']); 
$row['forumteilen'] = '<a href="" rel="tooltip" title="Teilen" data-toggle="modal" data-target=".forumteilen'.$row['i'].'"><i class="fa fa-share-alt" aria-hidden="true"></i></a>';
$row['teilenpermalink'] = '<label>Permalink</label><input class="form-control" value="' . $row['teilenurl'] . '?forum-showposts-' . $row['TID'] . '-p' . $row['page'] . '#' . $row['id'] . '" readonly="readonly" type="text" onfocus="this.select()"><br>';
$row['teilenbbcode'] = '<label>BBCode</label><input class="form-control" value="[url=' . $row['teilenurl'] . '?forum-showposts-' . $row['TID'] . '-p' . $row['page'] . '#' . $row['id'] . ']' . $aktTopicRow['name'] . '[/url]" readonly="readonly" type="text" onfocus="this.select()"><br>';
$row['teilenhtml'] = '<label>HTML</label><input class="form-control" value="&lt;a href=&quot;' . $row['teilenurl'] . '?forum-showposts-' . $row['TID'] . '-p' . $row['page'] . '#' . $row['id'] . '&quot;&gt;' . $aktTopicRow['name'] . '&lt;/a&gt;" readonly="readonly" type="text" onfocus="this.select()">';
# forum Teilen Ende 


	if ( $row['posts'] != 0 ) {
		$row['erst'] = '<a href="index.php?user-details-'.$row['erstid'].'"><b>'.$row['erst'].'</b></a>';
	} elseif ( $row['erstid'] != 0 ) {
        $row['rang'] = '<span class="label label-danger">gel&ouml;schter User</span>';
    }   

	if ($forum_rights['mods'] == TRUE AND $i>1) {
	  $row['delete'] = '<li class="divider"></li><li><a href="index.php?forum-delpost-'.$tid.'-'.$row['id'].'"><i class="fa fa-trash-o userdropspan" aria-hidden="true"></i>'.$lang['delete'].'</a></li>';
	}
	if ( $forum_rights['reply'] == TRUE AND loggedin() ) {
	  $row['change'] = '<li><a href="index.php?forum-editpost-'.$tid.'-'.$row['id'].'"><i class="fa fa-retweet userdropspan" aria-hidden="true"></i>'.$lang['change'].'</a></li>';
	}
	$row['posts']  = ($row['posts']?'<br><span class="label label-warning">Posts: '.$row['posts']:'').'</span>';
	$tpl->set_ar_out($row,1);

  $i++;
}
$tpl->set_ar_out( array ( 'SITELINK' => $MPL, 'ANTWORTEN' => $antworten ) , 2 );

if (loggedin()) {
  if ($menu->get(3) == 'topicalert') {
    if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid),0)) {
      db_query("DELETE FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid);
    } else {
      db_query("INSERT INTO prefix_topic_alerts (tid,uid) VALUES (".$tid.", ".$_SESSION['authid'].")");
    }
  }

  echo '';
  if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid),0)) {
    echo '<div class="com10newsinput"><a class="btn btn-warning btn-sm btn-block" href="index.php?forum-showposts-'.$tid.'-topicalert"><i class="fa fa-envelope-o" aria-hidden="true"></i> '.$lang['nomailonreply'].'</a></div>';
  } else {
    echo '<div class="com10newsinput"><a class="btn btn-default btn-sm btn-block" href="index.php?forum-showposts-'.$tid.'-topicalert"><i class="fa fa-envelope-o" aria-hidden="true"></i> '.$lang['mailonreply'].'</a></div>';
  }
}

if ( $forum_rights['mods'] == TRUE ) {
  $tpl->set ( 'status', ($aktTopicRow['stat'] == 1 ? $lang['close'] : $lang['open'] ) );
	$tpl->set ( 'festnorm', ($aktTopicRow['art'] == 0 ? $lang['fixedtopic'] : $lang['normaltopic'] ) );
	$tpl->set('tid',$tid);
	$tpl->out(3);
}
$design->footer();
?>