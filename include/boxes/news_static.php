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
$countnewsin    = db_result(db_query("SELECT COUNT(news_id) FROM `prefix_news`"), 0);
$countnewskat   = db_result(db_query("SELECT COUNT(news_kat) AS Anz FROM (SELECT DISTINCT news_kat FROM `prefix_news`) AS Src"), 0);
$useroneregist  = db_result(db_query('SELECT regist FROM prefix_user WHERE id = 1'), 0);
$countnewskom   = db_result(db_query("SELECT COUNT(id) FROM `prefix_koms` WHERE cat LIKE 'NEWS'"), 0);
$times          = time();
$difftage       = floor(($times - $useroneregist) / 86400);
if ($difftage == '0') {
    $difftage = '1';
}
$schnittnewsin = $countnewsin / $difftage;
$schnittnews   = round($schnittnewsin, 2);
if ($countnewskat == 1) {
    $countnewskatout = $countnewskat . ' Kategorie';
} else {
    $countnewskatout = $countnewskat . ' Kategorien';
}
if ($countnewskom == 1) {
    $countnewskoms = $countnewskom . ' Kommentar';
} else {
    $countnewskoms = $countnewskom . ' Kommentare';
}
$newsautor = db_result(db_query("SELECT COUNT(user_id) AS Anz FROM (SELECT DISTINCT user_id FROM `prefix_news`) AS Src"), 0);
if ($newsautor == 0) {
    $newsautoren = '';
} elseif ($newsautor == 1) {
    $newsautoren = $newsautor . ' Autor - ';
} else {
    $newsautoren = $newsautor . ' Autoren - ';
}
echo '<table class="statistictable"><tr>';
echo '<td class="statisticsimg"><h4><i class="fa fa-user-circle-o fa-2x text-primary pull-left" aria-hidden="true"></i></h4></td>';
echo '<td>
<h4>Benutzer Online <span class="badge">' . $gesamtonline . '</span></h4>
' . $useronlines . '  und ' . $gastonline . ' Besucher<br>Maximum: ' . $max . ' Besucher ( ' . $maxdates . ' )
</td></tr>';

echo '<tr><td colspan="2"><br>' . $useronlinelist . '<br><br></td>';
echo '</tr><tr>';
echo '<td colspan="2"><legend><i class="fa fa-bar-chart" aria-hidden="true"></i> News Statistik</legend></td></tr>';
echo '<tr><td colspan="2">' . $newsautoren . $countnewsin . ' News in ' . $countnewskatout . ' <span class="ilchforum_time"><br></span>( ' . $schnittnews . ' News pro Tag ) - ' . $countnewskoms . '</td>';
echo '</tr></table>';
?> 
