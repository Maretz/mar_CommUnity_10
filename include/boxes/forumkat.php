<?php


defined('main') or die('no direct access');

$forumcats    = "SELECT * FROM prefix_forumcats ORDER BY pos ASC";
$forumcatsout = db_query($forumcats);
$nocats       = db_result(db_query('SELECT COUNT(cid)  FROM `prefix_forums`'), 0);
if ($nocats == 0) {
    echo '<div class="text-center">Keine Kategorie vorhanden</div>';
} else {
    echo '<div class="boxenin">';
    while ($out = db_fetch_object($forumcatsout)) {
        $noforums = db_result(db_query('SELECT COUNT(cid)  FROM `prefix_forums` WHERE cid = ' . $out->id . ''), 0);
        if ($noforums == 0) {
            echo '';
        } else {
            echo '<a class="fccats" href="?forum-showcat-' . $out->id . '"><strong>' . $out->name . '</strong></a><br />';
            echo '<table class="boxenintable">';
            $forums    = 'SELECT name as forumname, id as forumsid, topics, view as forumrecht FROM prefix_forums WHERE cid = ' . $out->id . ' ORDER BY pos ASC';
            $forumsout = db_query($forums);
            while ($fout = db_fetch_object($forumsout)) {
                if (has_right($fout->forumrecht)) {
                    echo '<tr><td style="width: 10px;">&raquo;</td><td><a class="fcforums" href="?forum-showtopics-' . $fout->forumsid . '">' . $fout->forumname . '</a></td><td class="text-right" style="vertical-align:middle;"><span class="badge bagesboxenin">' . $fout->topics . '</span></td></tr>';
                }
            }
            echo '</table>';
        }
    }
    echo '</div>';
}
?> 