<?php 
#   Copyright by Manuel Staechele
#   Support www.ilch.de
setlocale(LC_TIME, "de_DE");
defined ('main') or die ( 'no direct access' );

$query = "SELECT a.id, a.name, a.rep,b.name as top, b.id as fid, c.erst as last, c.erstid, c.id as pid, c.time, a.rep, a.erst, a.hit, a.art, a.stat, d.name as kat
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
	LEFT JOIN prefix_forumcats d ON d.id = b.cid AND b.id = a.fid
  LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = b.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = b.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = b.start
WHERE ((".$_SESSION['authright']." <= b.view AND b.view < 1) 
   OR (".$_SESSION['authright']." <= b.reply AND b.reply < 1)
   OR (".$_SESSION['authright']." <= b.start AND b.start < 1)
	 OR vg.fid IS NOT NULL
	 OR rg.fid IS NOT NULL
	 OR sg.fid IS NOT NULL
	 OR -9 >= ".$_SESSION['authright'].")
ORDER BY a.rep DESC
LIMIT 0,4";
$resultID = db_query($query);
if (@db_num_rows($resultID) == 0) {
    echo '<span class="text-center">kein Beitrag vorhanden</span>';
} else {
echo '<table class="boxenintable">';
while ($row = db_fetch_assoc($resultID)) {
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
if ($fullHours == 0) {
$Stunde = $Minutes;
} elseif ($fullHours == 1) {
$Stunde = 'vor einer Stunde';
} else {
$Stunde = 'vor '. $fullHours .' Stunden';
}

 
$wochentag = strftime("%A", $times); 

        if (date("d.m.Y", $times) == date("d.m.Y")) {
            if ($fullHours < 12) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = strftime("Heute, %H:%M Uhr", $times);;
            }
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 24)) {
            if ($fullHours < 12) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = "Gestern, " . date("H:i", $times) . " Uhr";
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
            $row['date'] = strftime("%d. %B %Y", $times);
        }
        if (db_num_rows($resultID) < 2) {
            $boxeninend = '';
        } else {
            $boxeninend = 'boxeninend';
        }
	$row['page'] = ceil ( ($row['rep']+1)  / $allgAr['Fpanz'] );
	$row['ORD']  = forum_get_ordner($row['time'],$row['id'],$row['fid']);
echo'<tr>';	
echo '<td style="vertical-align:middle;" class="boxeninstart"><div class="toptopicbox"><strong>'. $row['rep'] .'</strong><div>Reply</div></div></td>';
echo '<td><small><span class="text-warning">Forum:</span> '.((strlen($row['top'])<35) ? $row['top'] : substr($row['top'],0,35).' ...').'</small><br /><a href="?forum-showposts-'.$row['id'].'-p'.$row['page'].'#'.$row['pid'].'">'.((strlen($row['name'])<40) ? $row['name'] : substr($row['name'],0,40).' ...').'</a></td>';
echo '</tr><tr><td class="' . $boxeninend . '" colspan="2"></td>';
echo '</tr>';
}
echo '</table>';
}
?>








