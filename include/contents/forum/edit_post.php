<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de

defined ('main') or die ( 'no direct access' );

if ( $forum_rights['reply'] == FALSE ) {
  $forum_failure[] = $lang['nopermission'];
	check_forum_failure($forum_failure);
}

# definie oid
$oid = escape($menu->get(3), 'integer');

$title = $allgAr['title'].' :: Forum :: '.aktForumCats($aktForumRow['kat'],'title').' :: '.$aktForumRow['name'].' :: '.$aktTopicRow['name'].' :: Beitrag &auml;ndern';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-'.$tid.'">'.$aktTopicRow['name'].'</a>'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();
			
if (!loggedin()) {
  echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><p>Gäste dürfen keine Beiträge editieren</p><p><a class="btn btn-default"href="index.php?user-regist">Registrieren</a>  <a class="btn btn-default" href="index.php?user-login">Einloggen</a></p></div>';
  $design->footer(1);
}

function getAvatar($id){
  $avatar = @db_result(db_query('SELECT avatar FROM prefix_user WHERE id = "'.$id.'"'),0);
  if (!empty($avatar) AND file_exists($avatar)) { 
        $avatar = '<img src="'.$avatar.'" alt="Avatar" />';
      } else {
        $avatar = '<img src="include/images/avatars/wurstegal.jpg" />';
      }
 return($avatar);
}

$row = @db_fetch_object(@db_query("SELECT txt,erstid FROM `prefix_posts` WHERE id = ".$oid));
if ($_SESSION['authid'] <> $row->erstid AND $forum_rights['mods'] == FALSE ) {
  echo $lang['nopermission'];  
  $design->footer(1);
}			

list($usec, $sec) = explode(" ", microtime());
$dppk_time = (float)$usec + (float)$sec;
$time = time();
if (!isset($_SESSION['klicktime'])) { $_SESSION['klicktime'] = 0; }


$txt = '';
if (isset($_POST['txt'])) {
  $txt = trim(escape($_POST['txt'], 'textarea'));
}
  
if ($_SESSION['klicktime'] > ($dppk_time - 15) OR empty($txt) OR !empty($_POST['priview'])) {

  $tpl = new tpl ( 'forum/postedit' );
      
  if (isset($_POST['priview'])) {
    $tpl->set_out('txt', bbcode(unescape($txt)), 0);
  }
  
  if (empty($txt)) {
    $txt = $row->txt;
  }
  
  $ar = array (
    'tid' => $tid,
    'oid' => $oid,
    'txt' => (isset($_POST['priview']) ? escape_for_fields(unescape($txt)) : escape_for_fields($txt)),
    'SMILIES' => getsmilies()
  );
  $tpl->set_ar_out($ar,1);
  $erg = db_query('SELECT erst, txt, time FROM `prefix_posts` WHERE tid = "'.$tid.'" ORDER BY time DESC LIMIT 0,5');
  while ($row = db_fetch_assoc($erg)) {
    $row['txt'] = bbcode($row['txt']);
        $row['datum']  = date('d.m.y - H:i', $row['time']);
    $ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['erst'].'"'),0);
   $row['avatar']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="pull-left showforumavatar" src="'.$ergava.'" alt="Avatar" />' : '<img class="pull-left showforumavatar" src="include/images/avatars/wurstegal.jpg" />';
    $tpl->set_ar_out($row, 2);
  }
  $tpl->out(3);  
} else {
  $s = preg_quote($lang['postlastchangedby']);
  if (preg_match("/.*".$s." ([^\ ])* am \d\d\.\d\d\.\d\d\d\d - \d\d:\d\d:\d\d$/", $txt)) {
    $txt = preg_replace("/".$s." ([^\ ])* am \d\d\.\d\d\.\d\d\d\d - \d\d:\d\d:\d\d$/", $lang['postlastchangedby'].' '.$_SESSION['authname'].' am '.date("d.m.Y - H:i:s"), $txt);
  } else {
    $txt .= "\n\n\n[right][size=12]" .$lang['postlastchangedby'].' '.$_SESSION['authname'].' am '.date("d.m.Y - H:i:s").' Uhr[/size][/right]';
  }
  
  db_query("UPDATE `prefix_posts` set txt = '".$txt."' WHERE id = ".$oid);

	$page = ceil ( ($aktTopicRow->rep+1)  / $allgAr['Fpanz'] );
  wd('index.php?forum-showposts-'.$tid.'-p'.$page.'#'.$oid,$lang['changepostsuccessful']);
}

$design->footer();
?>