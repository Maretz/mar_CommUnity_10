<?php
defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$comAbf = "SELECT * FROM `prefix_koms` WHERE cat LIKE 'NEWS' ORDER BY id DESC LIMIT 0,5";
$comErg = db_query($comAbf);

if (db_num_rows($comErg) > 0) {
    echo '<table class="boxenintable">';
    while ($comRow = db_fetch_object($comErg)) {
        $link         = 'index.php?news-' . $comRow->uid;
        $name         = $comRow->name;
        $comavatar    = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $name . '"'), 0);
        $text         = bbcode($comRow->text);
        $avatar       = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="boxenintableavatar" src="' . $comavatar . '" alt="Avatar" />' : '<img class="boxenintableavatar" src="include/images/avatars/wurstegal.jpg" />';
        $diffkom      = time() - $comRow->time;
        $fullHourskom = intval($diffkom / 60 / 60);
        $Minuteskom   = intval(($diffkom / 60) - (60 * $fullHourskom));
        if ($Minuteskom == 0) {
            $Minuteskom = 'Gerade eben';
        } elseif ($Minuteskom == 1) {
            $Minuteskom = 'vor einer Minute';
        } else {
            $Minuteskom = 'vor ' . $Minuteskom . ' Minuten';
        }
        if ($fullHourskom == 0) {
            $Stundenkom = $Minuteskom;
        } elseif ($fullHourskom == 1) {
            $Stundenkom = 'vor einer Stunde';
        } else {
            $Stundenkom = 'vor ' . $fullHourskom . ' Stunden';
        }
        $wochentagkom = strftime("%A", $comRow->time);
        
        if (date("d.m.Y", $comRow->time) == date("d.m.Y")) {
            if ($fullHourskom < 12) {
                $komtime = $Stundenkom;
            } else {
                $komtime = "Heute, " . date("H:i", $comRow->time) . " Uhr";
            }
        } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 24)) {
            if ($fullHourskom < 12) {
                $komtime = $Stundenkom;
            } else {
                $komtime = "Gestern, " . date("H:i", $comRow->time) . " Uhr";
            }
        } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 48)) {
            $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
        } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 72)) {
            $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
        } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 96)) {
            $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
        } else {
            $komtime = strftime("%d. %B %Y", $comRow->time);
        }
        if ($comRow->time == 0) {
            $komtime = '';
        }
        if (db_num_rows($comErg) < 2) {
            $boxeninend = '';
        } else {
            $boxeninend = 'boxeninend';
        }
        $newsrecht = @db_result(db_query('SELECT news_recht FROM prefix_news WHERE news_id = "' . $comRow->uid . '"'), 0);
        if (has_right($newsrecht)) {
            echo '<tr>';
            echo '<td class="bineavatartd boxeninstart">' . $avatar . '</td>';
            echo '<td class="boxeninstart"><span class="pull-right"><a rel="tooltip" title="zum Kommentar" href="' . $link . '#comments"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></span><strong>' . $name . '</strong><br /><small>' . $komtime . '</small></td>';
            echo '</tr><tr><td class="' . $boxeninend . '" colspan="2"><div class="boxeninvorschau">' . $text . '</div></td>';
            echo '</tr>';
        }
    }
    echo '</table>';
} else {
    echo '<span class="text-center">Kein Kommentar vorhanden</span>';
}

?> 