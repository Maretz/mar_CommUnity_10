<?php

defined('main') or die('no direct access');

$sql = "SELECT * FROM `prefix_downloads` ORDER by downs DESC LIMIT 0,5";

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
    $row->ssurl = '<img class="boxenintableavatar" src="'. $row->ssurl .'" alt="Image">';
echo'<tr>';	
echo '<td class="bineavatartd boxeninstart">'. $row->ssurl .'</td>';
echo '<td><a href="?downloads-show-'. $row->id .'">'. $row->name .' ('. $row->version .')</a><br /><small>'. $row->downs .'  mal heruntergeladen</small></td>';
echo '</tr><tr><td class="' . $boxeninend . '" colspan="2"></td>';
echo '</tr>';
    }
echo '</table>';
}
?> 