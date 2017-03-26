<?php
#   Onlinelist mar_CommUnity 10

defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$title  = $allgAr['title'] . ' :: Benutzer Online';
$hmenu  = 'Benutzer Online';
$design = new design($title, $hmenu);
$design->header();
echo '<div class="com10newsinput">';
echo '<div class="btn-group-plaze"><div class="btn-group">
<a href="?user" class="btn btn-default btn-xs">Mitglieder Liste</a><a href="?onlinelist" class="btn btn-default btn-xs">Benutzer Online</a><a href="?user#usersearch" class="btn btn-default btn-xs">Mitgliedersuche</a>
</div></div>';
$dif = date('Y-m-d H:i:s', time() - USERUPTIME);
$useron = db_result(db_query("SELECT COUNT(uid)  FROM `prefix_online` WHERE uid > 0 and uptime > '" . $dif . "'"), 0);
echo'<legend>Benutzer Online <span class="badge">'. $useron .'</span></legend>';
$erg = db_query("SELECT * FROM prefix_online WHERE uid>0 and uptime > '" . $dif . "' ORDER BY uid DESC");
if (@db_num_rows($erg) == 0) {
    echo '<div class="text-center text-muted"><small>Im Moment ist kein Benutzer online.</small><br /><br /></div>';
} else {
echo '<div class="row rowuserlist">';
while ($row = db_fetch_assoc($erg)) {
$row['user']  = @db_result(db_query('SELECT name FROM prefix_user WHERE id = "' . $row['uid'] . '"'), 0);
$comavatar = @db_result(db_query('SELECT avatar FROM prefix_user WHERE id = "' . $row['uid'] . '"'), 0);
$row['avatar']  = (!empty($comavatar) AND file_exists($comavatar)) ? '<img src="' . $comavatar . '" alt="Avatar" />' : '<img src="include/images/avatars/wurstegal.jpg" />';

$onlinetimestramp = new DateTime($row['uptime']);
$times = $onlinetimestramp->getTimestamp();
$diff = time() - $times; 
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
        } else {
            $row['date'] = strftime("%d. %B %Y", $times);
        }
echo '<div class="col-sm-6userlist"><div class="userlistbg">';
echo '<table><tr>';
echo '<td class="userlistdtavatar">'. $row['avatar'] .'</td>';
echo'<td><a class="userlistbglink" href="index.php?user-details-'. $row['uid'] .'">'. $row['user'] .'</a> '. $row['date'] .'<br /><small>IP Adresse: '. $row['ipa'] .'</small></td>';
echo '</tr></table>';
echo '</div></div>';
}
echo '</div>';
}
$dif = date('Y-m-d H:i:s', time() - USERUPTIME);
$guest = db_result(db_query("SELECT COUNT(uid)  FROM `prefix_online` WHERE uid = 0 and uptime > '" . $dif . "'"), 0);
echo'<legend>G&auml;ste <span class="badge">'. $guest .'</span></legend>';
$erg = db_query("SELECT * FROM prefix_online WHERE uid=0 and uptime > '" . $dif . "' ORDER BY uid DESC");
if (@db_num_rows($erg) == 0) {
    echo '<div class="text-center text-muted"><small>Im Moment ist kein Gast online.</small><br /></div>';
} else {
echo '<div class="row rowuserlist">';
while ($row = db_fetch_assoc($erg)) {
$row['user']  = 'Gast';
$row['avatar']  = '<img src="include/images/avatars/wurstegal.jpg" />';

$onlinetimestramp = new DateTime($row['uptime']);
$times = $onlinetimestramp->getTimestamp();
$diff = time() - $times; 
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
        } else {
            $row['date'] = strftime("%d. %B %Y", $times);
        }
echo '<div class="col-sm-6userlist"><div class="userlistbg">';
echo '<table><tr>';
echo '<td class="userlistdtavatar">'. $row['avatar'] .'</td>';
echo'<td><a class="userlistbglink" href="">'. $row['user'] .'</a> '. $row['date'] .'<br /><small>IP Adresse: '. $row['ipa'] .'</small></td>';
echo '</tr></table>';
echo '</div></div>';
}
echo '</div>';
}
echo '</div>';
$design->footer();

?> 