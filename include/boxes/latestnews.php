<?php
defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$abf = 'SELECT
          a.news_kat as kate,
          a.news_time,      
          a.news_title as title,
          a.news_kat as kate,
          a.news_id as id,      
          b.name as username,
          b.id as userid        
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= ' . $_SESSION['authright'] . '
          ORDER BY a.news_time DESC
          LIMIT 0,5';
$erg = db_query($abf);
if (loggedin()) {
    $admin = '';
    if (user_has_admin_right($menu, false)) {
        $admin = '<a href="admin.php?news">jetzt eine News erstellen</a>';
    }
}
if (@db_num_rows($erg) == 0) {
    echo '<span class="text-center">kein Newseintrag vorhanden<br />' . $admin . '</span>';
} else {
    echo '<table class="boxenintable">';
    while ($row = db_fetch_object($erg)) {
        if (file_exists('include/images/news/' . $row->kate . '.jpg')) {
            $row->katen = 'include/images/news/' . $row->kate . '.jpg';
        } elseif (file_exists('include/images/news/' . $row->kate . '.gif')) {
            $row->katen = 'include/images/news/' . $row->kate . '.gif';
        } elseif (file_exists('include/images/news/' . $row->kate . '.png')) {
            $row->katen = 'include/images/news/' . $row->kate . '.png';
        } elseif (file_not_exists) {
            $row->katen = 'include/images/news/noimage.png';
        }
        $newstimestramp = new DateTime($row->news_time);
        $timesnews      = $newstimestramp->getTimestamp();
        $diffnews       = time() - $timesnews;
        $fullHoursnews  = intval($diffnews / 60 / 60);
        $Minutesnews    = intval(($diffnews / 60) - (60 * $fullHoursnews));
        if ($Minutesnews == 0) {
            $Minutesnews = 'gerade eben';
        } elseif ($Minutesnews == 1) {
            $Minutesnews = 'vor einer Minute';
        } else {
            $Minutesnews = 'vor ' . $Minutesnews . ' Minuten';
        }
        if ($fullHoursnews == 0) {
            $Stundenews = $Minutesnews;
        } elseif ($fullHoursnews == 1) {
            $Stundenews = 'vor einer Stunde';
        } else {
            $Stundenews = 'vor ' . $fullHoursnews . ' Stunden';
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
        } else {
            $row->newnewstime = strftime("%d. %B %Y", $timesnews);
        }
        if (db_num_rows($erg) < 2) {
            $boxeninend = '';
        } else {
            $boxeninend = 'boxeninend';
        }

        function shortText($string,$lenght) {
        if(strlen($string) > $lenght) {
        $string = substr($string,0,$lenght)."...";
        $string_ende = strrchr($string, " ");
        $string = str_replace($string_ende," ...", $string);
        }
        return $string;
        } 
        echo '<tr>';
        echo '<td class="bineavatartd boxeninstart"><img class="boxenintableavatar" src="' . $row->katen . '" alt=""></td>';
        echo '<td class="boxenstart"><small><i class="fa fa-caret-right" aria-hidden="true"></i> ' . $row->kate . '</small><br /><a class="bilink" href="index.php?news-' . $row->id . '">' . shortText($row->title,45) . '</a><br /><small><a href="?user-details-'. $row->userid .'">'. $row->username .'</a> - ' . $row->newnewstime . '</small></td>';
        echo '</tr><tr><td class="' . $boxeninend . '" colspan="2"></td>';
        echo '</tr>';
    }
}
echo '</table>';
?> 