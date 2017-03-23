<?php

defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");

$gesamtonline = ges_online();
$gastonline   = ges_gast_online();
$useronline   = ges_user_online();
if ($useronline == 1) {
    $useronlines = $useronline . ' Mitglied';
} else {
    $useronlines = $useronline . ' Mitglieder';
}
$useronlinelist = user_online_liste();
$max            = db_result(db_query('SELECT MAX(count) FROM `prefix_counter`'), 0);
$maxdate        = db_result(db_query('SELECT date FROM `prefix_counter` WHERE count = "' . $max . '"'));
$newtimestramp  = new DateTime($maxdate);
$maxdater       = $newtimestramp->getTimestamp();
$maxdates       = strftime("%d. %B %Y", $maxdater);
$countdownsin    = db_result(db_query("SELECT COUNT(id) FROM `prefix_downloads`"), 0);
$countdownkat   = db_result(db_query("SELECT COUNT(id) FROM `prefix_downcats`"), 0);
$summedown   = db_result(db_query("SELECT SUM(downs) as summe FROM `prefix_downloads`"), 0);
if ($summedown == 1) {
$summedown = $summedown .' Download';
} elseif ($summedown == 0){
$summedown = '0 Downloads';
} else {
$summedown = $summedown .' Downloads';
}
$votedown   = db_result(db_query("SELECT SUM(vote_klicks) as summe FROM `prefix_downloads`"), 0);
if ($votedown  == 1) {
$votedown = $votedown .' Bewertung';
} elseif ($votedown  == 0) {
$votedown = '0 Bewertungen';
} else {
$votedown = $votedown .' Bewertungen';
}
$useroneregist  = db_result(db_query('SELECT regist FROM prefix_user WHERE id = 1'), 0);
$times          = time();
$difftage       = floor(($times - $useroneregist) / 86400);
if ($difftage == '0') {
    $difftage = '1';
}
$schnittdown = $summedown / $difftage;
$schnittdowns   = round($schnittdown, 2);
if ($schnittdowns == 1) {
$schnittdowns = $schnittdowns .' Download pro Tag';
} else {
$schnittdowns = $schnittdowns .' Downloads pro Tag';
}
if ($countdownsin == 1) {
$countdownsin = $countdownsin .' Datei';

} else {
$countdownsin = $countdownsin .' Dateien';
}
if ($countdownkat == 1) {
$countdownkat = $countdownkat .' Kategorie';
} else {
$countdownkat = $countdownkat .' Kategorien';
}
$downupload = nicebytes(dirsize('include/downs/downloads/downs/') + dirsize('include/downs/downloads/user_upload/'));
if ($countdownsin == 0) {
$downupload = '0 kB';
}
$downautor = db_result(db_query("SELECT COUNT(creater) AS Anz FROM (SELECT DISTINCT creater FROM `prefix_downloads`) AS Src"), 0);
if ($downautor == 0) {
    $downautor = '';
} elseif ($downautor == 1) {
    $downautor = $downautor . ' Autor - ';
} else {
    $downautor = $downautor . ' Autoren - ';
}
echo '<table class="statistictable"><tr>';
echo '<td class="statisticsimg"><h4><i class="fa fa-user-circle-o fa-2x text-primary pull-left" aria-hidden="true"></i></h4></td>';
echo '<td>
<h4>Benutzer Online <span class="badge">' . $gesamtonline . '</span></h4>
' . $useronlines . '  und ' . $gastonline . ' Besucher<br>Maximum: ' . $max . ' Besucher ( ' . $maxdates . ' )
</td></tr>';

echo '<tr><td colspan="2"><br>' . $useronlinelist . '<br><br></td>';
echo '</tr><tr>';
echo '<td colspan="2"><legend><i class="fa fa-bar-chart" aria-hidden="true"></i> Downloads Statistik</legend></td></tr>';
echo '</tr></table>';
?> 
