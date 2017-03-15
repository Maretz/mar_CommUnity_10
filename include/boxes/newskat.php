<?php

defined('main') or die('no direct access');

$sql = "SELECT DISTINCT news_kat FROM `prefix_news` ORDER by news_kat DESC";

$erg = db_query($sql);
if (@db_num_rows($erg) == 0) {
    echo '<span class="text-center">Keine Kategorie vorhanden</span>';
} else {
    echo '<div class="boxenin">';
    while ($row = db_fetch_object($erg)) {
        $newskat   = $row->news_kat;
        #$newskaturl = str_replace(' ', '_', $newskat);
        $newsinkat = db_result(db_query("SELECT COUNT(news_id) FROM prefix_news WHERE news_kat LIKE '$newskat%' ORDER by news_kat DESC"), 0);
        echo $row->news_kat . '<span class="badge bagesboxenin">' . $newsinkat . '</span><br />';
    }
    echo '</div>';
}
?> 