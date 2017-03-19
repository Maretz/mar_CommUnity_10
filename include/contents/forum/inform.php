<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de
#   inform.php by Mairu
 
defined ('main') or die ( 'no direct access' );
 
$title = $allgAr['title'].' :: Forum :: Benachrichtigung';
$hmenu  = 'Forum <b> &raquo; </b> Benachrichtigung';
$design = new design ( $title , $hmenu, 1);
$design->header();
 
 
$postid = ($menu->getA(2) == 'p' ? $menu->getE(2) : '');
if (isset($_POST['submit'])) $postid = $_POST['postid'];
if (empty($postid) OR @db_result(db_query("SELECT COUNT(*) FROM `prefix_posts` WHERE id = $postid"),0) != 1) 
echo '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Diese Seite wurde falsch aufgerufen!<br /><br /><a class="btn btn-default" href="javascript:history.back()">zur&uuml;ck</a></div>';
else {
$frm = db_fetch_object(db_query("SELECT tid,fid,erst,erstid,time FROM `prefix_posts` WHERE id = $postid"));
  if (isset($_POST['submit']) AND $_POST['mod'] != 'noone'){
    $pmtxt = "Benachrichtigung durch: {$_SESSION['authname']}\n
    User: $frm->erst ($frm->erstid)\n
    [url=http://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}?forum-showposts-$frm->tid#$postid]Link zum Post[/url]
     vom ".date('d.m.Y - H:i',$frm->time)."\n
    Kommentar: {$_POST['reason']}\n";
    sendpm($_SESSION['authid'],$_POST['mod'],'Forumsbenachrichtigung',$pmtxt);
    echo '<div class="alert alert-success" role="alert"><i class="fa fa-check" aria-hidden="true"></i> Vielen Dank f&uuml;r die Benachichtigung. Wir werden uns umgehend diesem Thema annehmen.<br /><br /><a class="btn btn-default" href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?forum-showposts-'.$frm->tid.'">Zur&uuml;ck zum Thema</a></div>';   
    }    
  else {
    if (isset($_POST['submit']) AND $_POST['mod'] != 'none') echo '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Bitte w&auml;hlen sie einen Adressat aus.</div>';
    $mods = '';
    $rechte = array();
    $sql = db_query("SELECT name,id FROM `prefix_grundrechte` WHERE id <= -7");
    while ($row = db_fetch_object($sql)) $rechte[$row->id]=$row->name;
    $sql = db_query("SELECT id,name,recht FROM `prefix_user` WHERE recht <= -7 ORDER BY recht ASC, name DESC");
    while ($row = db_fetch_object($sql)) $mods .= "<option value=\"$row->id\">$row->name (".$rechte[$row->recht].")</option>\n";
    $sql = db_query("SELECT b.id,b.name FROM prefix_forummods a LEFT JOIN prefix_user b ON b.id = a.uid WHERE a.fid = ".$frm->fid);
    while ($row = db_fetch_object($sql)) $mods .= "<option value=\"$row->id\">$row->name (Moderator)</option>\n";
    $out = array( 'POSTID' => '<input type="hidden" name="postid" value="'.$postid.'" />',
                  'MODS' => $mods,
                  'TXT' => $_POST['reason']); 
    $tpl = new tpl('forum/inform');
    $tpl->set_ar_out($out,0); 
    }
  }
  $design->footer();
?>