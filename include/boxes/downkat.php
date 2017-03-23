<?php

defined('main') or die('no direct access');

$sql = "SELECT name, id, cat as dlcat FROM `prefix_downcats` ORDER by pos DESC";

$erg = db_query($sql);
if (@db_num_rows($erg) == 0) {
    echo '<span class="text-center">Keine Kategorie vorhanden</span>';
} else {
    echo '<div class="boxenin">';
    while ($row = db_fetch_object($erg)) {
        $downcat   = $row->id;
        $row->name = '<a href="?downloads-'. $row->id .'">'. $row->name .'</a>';
        $downinkat = db_result(db_query("SELECT COUNT(cat) FROM prefix_downloads WHERE cat LIKE '$downcat%' ORDER by cat DESC"), 0);
        echo $row->name . '<span class="badge bagesboxenin">' . $downinkat . '</span><br />';
    }
    echo '</div>';
}
?> 