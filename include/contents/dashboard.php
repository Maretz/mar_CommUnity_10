<?php
#   Dashboard by Maretz.eu Ilch CMS

defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$title  = $allgAr['title'] . ' :: Dashboard';
$hmenu  = 'Dashboard';
$design = new design($title, $hmenu);
$design->header();
echo '<div class="com10newsinput">';
$erg = db_query("SELECT a.id, a.name,a.erst as autor, a.rep,b.name as top, b.id as fid, c.erst as last,c.txt, c.erstid, c.id as pid, c.time, a.rep, a.erst, a.hit, a.art, a.stat, d.name as kat
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
    LEFT JOIN prefix_forumcats d ON d.id = b.cid AND b.id = a.fid
  LEFT JOIN prefix_groupusers vg ON vg.uid = " . $_SESSION['authid'] . " AND vg.gid = b.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = " . $_SESSION['authid'] . " AND rg.gid = b.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = " . $_SESSION['authid'] . " AND sg.gid = b.start
WHERE ((" . $_SESSION['authright'] . " <= b.view AND b.view < 1) 
   OR (" . $_SESSION['authright'] . " <= b.reply AND b.reply < 1)
   OR (" . $_SESSION['authright'] . " <= b.start AND b.start < 1)
     OR vg.fid IS NOT NULL
     OR rg.fid IS NOT NULL
     OR sg.fid IS NOT NULL
     OR -9 >= " . $_SESSION['authright'] . ")
ORDER BY c.id DESC
LIMIT 0,5");
echo '<legend>Letzte Forum Aktivit&auml;ten</legend>';
if (loggedin()) {
    $admin = '';
    if (user_has_admin_right($menu, false)) {
        $admin = '<br><a href="admin.php?forum">neues Forum erstellen</a>';
    }
}
if ( @db_num_rows($erg) == 0 ) {
    echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
    echo '<table class="commenttable">';
    echo '<tr>';
    echo '<td style="text-align:center;">kein Forumeintrag vorhanden'.$admin.'</td>';
    echo '</tr>';
    echo '</table></div>';
} 
while ($row = db_fetch_assoc($erg)) {
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

    $autorid        = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $row['autor'] . '"'), 0);
    $comavatar      = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $row['last'] . '"'), 0);
    $row['avatar']  = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="drashboardavatar" src="' . $comavatar . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
    $row['texte']   = bbCode($row['txt']);
    $row['page']    = ceil(($row['rep'] + 1) / $allgAr['Fpanz']);
    $row['autore']  = '<a class="drashboardlink" href="?forum-showposts-' . $row['id'] . '">' . $row['autor'] . '</a>';
    $row['name']    = 'Hat auf das Thema <a class="drashboardlink" href="?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '">' . $row['name'] . '</a> von ' . $row['autore'] . ' zuletzt geantwortet.';
    $row['autores'] = '<a class="drashboardlink" href="?user-details-' . $row['erstid'] . '">' . $row['last'] . '</a>';
    $row['kat']     = $row['kat'];
    $row['right']   = '<a class="drashboardlink" data-toggle="tooltip" data-placement="top"  title="zum Beitrag" href="?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
    
    echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
    echo '<table class="commenttable">';
    echo '<tr>';
    echo '<td style="vertical-align:top;width:55px;">' . $row['avatar'] . '</td>';
    echo '<td style="vertical-align:top;"><span class="drashboardweiter">' . $row['right'] . '</span>' . $row['autores'] . ' <small>- ' . $row['date'] . '</small><br>
' . $row['name'] . '</td></tr><tr><td colspan="2"><div class="drashboardin">' . $row['texte'] . '</div></td>';
    echo '</tr>';
    echo '</table></div>';
    
}


$abf = 'SELECT
          a.news_kat as kate, 
          a.news_time,
          a.news_title as title,
          a.news_id as id, 
          a.news_text as txt,    
          b.name as username,
          b.id as userid        
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= ' . $_SESSION['authright'] . '
          ORDER BY a.news_time DESC
          LIMIT 0,5';
echo '<br><legend>Letzte News Eintr&auml;ge</legend>';
$erg2 = db_query($abf);
if (loggedin()) {
    $admin = '';
    if (user_has_admin_right($menu, false)) {
        $admin = '<a href="admin.php?news">jetzt eine News erstellen</a>';
    }
}
if (@db_num_rows($erg2) == 0) {
        echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
        echo '<table class="commenttable">';
        echo '<tr>';
        echo '<td class="text-center">kein Newseintrag vorhanden<br>' . $admin .'</td>';
        echo '</tr>';
        echo '</table></div>';
} else {
    while ($row = db_fetch_object($erg2)) {
    $newstimestramp = new DateTime($row->news_time);
    $timesnews = $newstimestramp->getTimestamp();
$diffnews = time() - $timesnews; 
$fullHoursnews = intval($diffnews/60/60); 
$Minutesnews = intval(($diffnews/60)-(60*$fullHoursnews));
if ($Minutesnews == 0) {
$Minutesnews = 'gerade eben';
} elseif ($Minutesnews == 1) {
$Minutesnews = 'vor einer Minute';
} else {
$Minutesnews = 'vor '. $Minutesnews .' Minuten';
}
if ($fullHoursnews == 0) {
$Stundenews = $Minutesnews;
} elseif ($fullHoursnews == 1) {
$Stundenews = 'vor einer Stunde';
} else {
$Stundenews = 'vor '. $fullHoursnews .' Stunden';
}
$wochentagnews = strftime("%A", $timesnews); 

            if (date("d.m.Y", $timesnews) == date("d.m.Y")) {
                if ($fullHoursnews < 12) {
                    $row->newnewstime = $Stundenews;
                } else {
                    $row->newnewstime = "Heute, " . date("H:i", $timesnews) . " Uhr";
                }
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 24)) {
                if ($fullHoursnews < 12) {
                    $row->newnewstime = $Stundenews;
                } else {
                    $row->newnewstime = "Gestern, " . date("H:i", $timesnews) . " Uhr";
                }
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 48)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 72)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 96)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 120)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } else {
                $row->newnewstime = strftime("%d. %B %Y", $timesnews);
            }

        $comavatar2   = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $row->username . '"'), 0);
        $row->avatar2 = (!empty($comavatar2) AND file_exists($comavatar2)) ? '<img class="drashboardavatar" src="' . $comavatar2 . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
        $row->text    = bbCode($row->txt);
        $row->title   = '<a class="drashboardlink" href="?news-' . $row->id . '">' . $row->title . '</a>';
        $row->titl    = 'Hat die News ' . $row->title . ' in der Kategorie <strong>' . $row->kate . '</strong> eingetragen.';
        $row->right   = '<a class="drashboardlink" data-toggle="tooltip" data-placement="top"  title="zur News" href="?news-' . $row->id . '"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
        echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
        echo '<table class="commenttable">';
        echo '<tr>';
        echo '<td style="vertical-align:top;width:55px;">' . $row->avatar2 . '</td>';
        echo '<td style="vertical-align:top;"><span class="drashboardweiter">' . $row->right . '</span><a class="drashboardlink" href="?user-details-' . $row->userid . '">' . $row->username . '</a> <small>- ' . $row->newnewstime . '</small><br>
' . $row->titl . '</td></tr><tr><td colspan="2"><div class="drashboardin">' . $row->text . '</div></td>';
        echo '</tr>';
        echo '</table></div>';
        
    }
}

$comAbf = "SELECT * FROM `prefix_koms` ORDER BY id DESC LIMIT 0,5";
$comErg = db_query($comAbf);

if (db_num_rows($comErg) > 0) {
    echo '<br><legend>Letzte Kommentare</legend>';
    while ($comRow = db_fetch_object($comErg)) {
            $diffkom3      = time() - $comRow->time;
            $fullHourskom = intval($diffkom3 / 60 / 60);
            $Minuteskom   = intval(($diffkom3 / 60) - (60 * $fullHourskom));
            if ($Minuteskom == 0) {
                $Minuteskom = '- gerade eben';
            } elseif ($Minuteskom == 1) {
                $Minuteskom = '- vor einer Minute';
            } else {
                $Minuteskom = '- vor ' . $Minuteskom . ' Minuten';
            }
            if ($fullHourskom == 0) {
                $Stundenkom = $Minuteskom;
            } elseif ($fullHourskom == 1) {
                $Stundenkom = '- vor einer Stunde';
            } else {
                $Stundenkom = '- vor ' . $fullHourskom . ' Stunden';
            }
            $wochentagkom = strftime("- %A", $comRow->time);
            
            if (date("d.m.Y", $comRow->time) == date("d.m.Y")) {
                if ($fullHourskom < 12) {
                    $komtime = $Stundenkom;
                } else {
                    $komtime = "- Heute, " . date("H:i", $comRow->time) . " Uhr";
                }
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 24)) {
                if ($fullHourskom < 12) {
                    $komtime = $Stundenkom;
                } else {
                    $komtime = "- Gestern, " . date("H:i", $comRow->time) . " Uhr";
                }
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 48)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 72)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 96)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 120)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } else {
                $komtime = strftime("- %d. %B %Y", $comRow->time);
            }
            if ($comRow->time == 0) {
                $komtime = '';
            }
  $cid = escape($menu->get(2), 'integer');
        if ($comRow->cat == 'NEWS') {
            $link        = 'index.php?news-' . $comRow->uid . '#comments';
            $namekate    = 'die News';
            $nameeintrag = @db_result(db_query('SELECT news_title FROM prefix_news WHERE news_id = "' . $comRow->uid . '"'), 0);
            $namekat     = '';
        } elseif ($comRow->cat == 'GBOOK') {
            $link        = 'index.php?gbook-show-' . $comRow->uid;
            $namekate    = 'den G&auml;stebucheintrag von';
            $nameeintrag = @db_result(db_query('SELECT name FROM prefix_gbook WHERE id = "' . $comRow->uid . '"'), 0);
            $namekat     = '';
        } elseif ($comRow->cat == 'WARSLAST') {
            $link        = 'index.php?wars-more-' . $comRow->uid;
            $namekate    = 'den War gegen';
            $nameeintrag = @db_result(db_query('SELECT gegner FROM prefix_wars WHERE id = "' . $comRow->uid . '"'), 0);
            $nameeintragtag = @db_result(db_query('SELECT tag FROM prefix_wars WHERE id = "' . $comRow->uid . '"'), 0);
        if ($nameeintrag == '0') {
            $nameeintrag = $nameeintragtag;
         } else {
            $nameeintrag = $nameeintrag;
         }
        } elseif ($comRow->cat == 'GALLERYIMG') {                
                $namekate    = 'das Bild ';
                $endung = @db_result(db_query('SELECT endung FROM prefix_gallery_imgs WHERE id = "' . $comRow->uid . '"'), 0);
                $namebild = @db_result(db_query('SELECT datei_name FROM prefix_gallery_imgs WHERE id = "' . $comRow->uid . '"'), 0);
                $nameeintrag = $namebild.'.'.$endung;           
                $bildid      = @db_result(db_query('SELECT cat FROM prefix_gallery_imgs WHERE datei_name = "' . $namebild . '"'), 0);
                $link        = 'index.php?gallery-' . $bildid ;
                $namekat     = @db_result(db_query('SELECT name FROM prefix_gallery_cats WHERE id = "' . $bildid . '"'), 0);
                if ($bildid  == '0') {
                $namekat     = '';
                } else {
                $namekat     = 'in der Kategorie <b>'. $namekat .'</b>';
              }
            }
        $name        = $comRow->name;
        $comavatar   = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $name . '"'), 0);
        $text        = bbcode($comRow->text);
        $avatars     = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="drashboardavatar" src="' . $comavatar . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
        $right       = '<a class="drashboardlink" data-toggle="tooltip" data-placement="top"  title="zum Kommentar" href="' . $link . '"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
        $userid      = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $name . '"'), 0);
        $nameeintarg = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $name . '"'), 0);
        $titel       = 'Hat ' . $namekate . ' <a class="drashboardlink" href="' . $link . '">' . $nameeintrag . '</a> ' . $namekat . ' kommentiert.';
        echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
        echo '<table class="commenttable">';
        echo '<tr>';
        echo '<td style="vertical-align:top;width:55px;">' . $avatars . '</td>';
        echo '<td style="vertical-align:top;"><span class="drashboardweiter">' . $right . '</span><a class="drashboardlink" href="?user-details-' . $userid . '">' . $name . '</a>  <span class="smalfont">' . $komtime . '</span><br>
' . $titel . '</td></tr><tr><td colspan="2"><div class="drashboardin">' . $text . '</div></td>';
        echo '</tr>';
        echo '</table></div>';
    }
} else {
    echo '<br><legend>Letzte Kommentare</legend>';
    echo '<div class="well wellsmnews" style="margin-bottom:-2px;border-radius: 0;">';
    echo '<table class="commenttable">';
    echo '<tr>';
    echo '<td class="text-center">Keine Kommentare vorhanden.</td>';
    echo '</tr>';
    echo '</table></div>';
}

echo '</div>';
$design->footer();

?> 