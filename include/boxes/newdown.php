<?php

defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$sql = "SELECT * FROM `prefix_downloads` ORDER by time DESC";

$erg = db_query($sql);
if (@db_num_rows($erg) == 0) {
    echo '<span class="text-center">Keine Datei vorhanden</span>';
} else {
echo '<table class="boxenintable">';
        if (db_num_rows($erg) < 2) {
            $boxeninend = '';
        } else {
            $boxeninend = 'boxeninend';
        }
    while ($row = db_fetch_object($erg)) {
        $downtimestramp = new DateTime($row->time);
        $timesdown      = $downtimestramp->getTimestamp();
        $diffdown       = time() - $timesdown;
        $fullHoursdown  = intval($diffdown / 60 / 60);
        $Minutesdown    = intval(($diffdown / 60) - (60 * $fullHoursdown));
        if ($Minutesdown == 0) {
            $Minutesdown = 'gerade eben';
        } elseif ($Minutesdown == 1) {
            $Minutesdown = 'vor einer Minute';
        } else {
            $Minutesdown = 'vor ' . $Minutesdown . ' Minuten';
        }
        if ($fullHoursdown == 0) {
            $Stundedown = $Minutesdown;
        } elseif ($fullHoursdown == 1) {
            $Stundedown = 'vor einer Stunde';
        } else {
            $Stundedown = 'vor ' . $fullHoursdown . ' Stunden';
        }
        $wochentagdown = strftime("%A", $timesdown);
        
        if (date("d.m.Y", $timesdown) == date("d.m.Y")) {
            if ($fullHoursdown < 12) {
                $row->newdowntime = $Stundedown;
            } else {
                $row->newdowntime = "Heute, " . date("H:i", $timesdown) . " Uhr";
            }
        } elseif (date("d.m.Y", $timesdown) == date("d.m.Y", time() - 60 * 60 * 24)) {
            if ($fullHoursdown < 12) {
                $row->newdowntime = $Stundedown;
            } else {
                $row->newdowntime = "Gestern, " . date("H:i", $timesdown) . " Uhr";
            }
        } elseif (date("d.m.Y", $timesdown) == date("d.m.Y", time() - 60 * 60 * 48)) {
            $row->newdowntime = "$wochentagdown, " . date("H:i", $timesdown) . " Uhr";
        } elseif (date("d.m.Y", $timesdown) == date("d.m.Y", time() - 60 * 60 * 72)) {
            $row->newdowntime = "$wochentagdown, " . date("H:i", $timesdown) . " Uhr";
        } elseif (date("d.m.Y", $timesdown) == date("d.m.Y", time() - 60 * 60 * 96)) {
            $row->newdowntime = "$wochentagdown, " . date("H:i", $timesdown) . " Uhr";
        } else {
            $row->newdowntime = strftime("%d. %B %Y", $timesdown);
        }
    $row->ssurl = '<img class="boxenintableavatar" src="'. $row->ssurl .'" alt="Image">';
echo'<tr>';	
echo '<td class="bineavatartd boxeninstart">'. $row->ssurl .'</td>';
echo '<td><a href="?downloads-show-'. $row->id .'">'. $row->name .' ('. $row->version .')</a><br /><small>'. $row->desc .'<br /><span class="text-warning">'. $row->newdowntime .'</span></small></td>';
echo '</tr><tr><td class="' . $boxeninend . '" colspan="2"></td>';
echo '</tr>';
    }
echo '</table>';
}
?> 