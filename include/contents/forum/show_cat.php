<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


$title = $allgAr['title'].' :: Forum :: '.aktForumCats($aktForumRow['kat'],'title');
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

function getAvatar($id){
  $avatar = @db_result(db_query('SELECT avatar FROM prefix_user WHERE id = "'.$id.'"'),0);
  if (!empty($avatar) AND file_exists($avatar)) { 
        $avatar = '<img src="'.$avatar.'" alt="Avatar" />';
      } else {
        $avatar = '<img src="include/images/avatars/wurstegal.jpg" />';
      }
 return($avatar);
}

$tpl = new tpl ( 'forum/show_cat' );
$tpl->out (0);

$cid = escape($menu->get(2), 'integer');

$q = "SELECT
  a.id, a.cid, a.name, a.besch,
  a.topics, a.posts, b.name as topic,
  c.id as pid, c.tid, b.rep, c.erst, c.time,
  a.cid, k.name as cname
FROM prefix_forums a
  LEFT JOIN prefix_forumcats k ON k.id = a.cid
  LEFT JOIN prefix_posts c ON a.last_post_id = c.id
  LEFT JOIN prefix_topics b ON c.tid = b.id
	
  LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = a.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = a.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = a.start
	
WHERE ((".$_SESSION['authright']." <= a.view AND a.view < 1) 
   OR (".$_SESSION['authright']." <= a.reply AND a.reply < 1)
   OR (".$_SESSION['authright']." <= a.start AND a.start < 1)
	 OR vg.fid IS NOT NULL
	 OR rg.fid IS NOT NULL
	 OR sg.fid IS NOT NULL
	 OR -9 >= ".$_SESSION['authright'].")
  AND k.id     = ".$cid."
ORDER BY k.pos, a.pos";
$erg1 = db_query($q);
$xcid = 0;
while ($r = db_fetch_assoc($erg1) ) {
  
  $r['topicl'] = $r['topic'];
  $r['topic']  = html_enc_substr($r['topic'],0,200);
  $r['ORD']    = forum_get_ordner($r['time'],$r['id']);
  $r['mods']   = getmods($r['id']);
  if($r['topics'] == '1') 
   {
   $r['topics'] = $r['topics'] .' Thema';
   } 
else 
   {
   $r['topics']  = $r['topics'] .' Themen';
   }
  if($r['posts'] == '1') 
   {
   $r['posts'] = $r['posts'] .' Beitrag';
   } 
else 
   {
   $r['posts']  = $r['posts'] .' Beitr&auml;ge';
   }
$times = $r['time'];
  if(date("d.m.Y",$times) == date("d.m.Y")) 
   {
   $r['datum'] = "<b style=\"color:#ff0000;\">Heute</b> ".date("H:i",$times)." Uhr";        
   } 
elseif (date("d.m.Y",$times) == date("d.m.Y",time()-60*60*24))
   {
   $r['datum'] = "Gestern ".date("H:i",$times)." Uhr";
   }
elseif (date("d.m.Y",$times) == date("d.m.Y",time()-60*60*48))
   {
   $r['datum'] = "vor 2 Tagen ";
   }
else 
   {
   $r['datum'] = "".date("d. M. Y",$times)."";
   }
    if($r['topics'] == 0) 
   {
   $r['datums'] = '<a href="index.php?forum-newtopic-'.$r['id'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Thema erstellen</a>';
   } 
else 
   {
   $r['datums']  = '- '. $r['datum'];
   }
     if($r['ORD'] == '<i class="fa fa-plus-circle colorplus" aria-hidden="true"></i>') 
   {
   $r['colorclosetopics2'] = 'colorclosetopics2';
   } 
else 
   {
   $r['colorclosetopics2']  = '';
   }
  $r['page']   = ceil ( ($r['rep']+1)  / $allgAr['Fpanz'] );
   $ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$r['erst'].'"'),0);
   if($r['topics'] == 0) 
   {
   $r['avatar'] = '';
   } 
else 
   {
   $r['avatar']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="pull-left showforumavatar" src="'.$ergava.'" alt="Avatar" />' : '<img class="pull-left showforumavatar" src="include/images/avatars/wurstegal.jpg" />';
   }
     if($r['topics'] == 0) 
   {
   $r['legend_letzteantwort'] = '<legend><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Kein Thema vorhanden</legend>';
   } 
else 
   {
   $r['legend_letzteantwort']  = '<legend><i class="fa fa-clock-o" aria-hidden="true"></i> Letzte Antwort</legend>';
   }
  $tpl->set_ar ($r);
  
  if ($r['cid'] <> $xcid) {
    $tpl->out(1);
    //Unterkategorien
    $sql = db_query("SELECT DISTINCT a.name as cname, a.id as cid FROM `prefix_forumcats` a LEFT JOIN `prefix_forums` b ON a.id = b.cid WHERE a.cid = {$r['cid']} AND a.id = b.cid ORDER BY a.pos, a.name");
    while ($ucat = db_fetch_assoc($sql)) {
      $tpl->set_ar_out($ucat,2);
    }
    //Unterkategorien - Ende
    $xcid = $r['cid'];
  }
  $tpl->out(3);
}
$tpl->out(4);
$design->footer();
?>