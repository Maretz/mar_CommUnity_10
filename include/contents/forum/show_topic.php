<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


# check ob ein fehler aufgetreten ist.
check_forum_failure($forum_failure);

$title = $allgAr['title'].' :: Forum :: '.aktForumCats($aktForumRow['kat'],'title').' :: '.$aktForumRow['name'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b>'.$aktForumRow['name'].$extented_forum_menu_sufix;
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
	
	$limit = $allgAr['Ftanz'];  // Limit 
  $page = ( $menu->getA(3) == 'p' ? $menu->getE(3) : 1 );
  $MPL = db_make_sites ($page , "WHERE fid = '$fid'" , $limit , '?forum-showtopics-'.$fid , 'topics' );
  $anfang = ($page - 1) * $limit;
  
	$tpl = new tpl ( 'forum/showtopic' );
	
	if ( $forum_rights['start'] == TRUE ) {
	  $tpl->set('NEWTOPIC', '<a class="btn btn-primary btn-xs" href="index.php?forum-newtopic-'.$fid.'">'.$lang['newtopic'].'</a>' );
	} else {
	  $tpl->set('NEWTOPIC','');
	}
  $tpl->set('MPL', $MPL);
	$tpl->set_out('FID', $fid, 0);
  
	$q = "SELECT a.id, a.name, a.rep, a.erst, a.hit, a.art, a.stat, b.time, b.erst as last, b.id as pid
	FROM prefix_topics a
	LEFT JOIN prefix_posts b ON a.last_post_id = b.id
	WHERE a.fid = {$fid}
	ORDER BY a.art DESC, b.time DESC
	LIMIT ".$anfang.",".$limit;
	$erg = db_query($q);
	if ( db_num_rows($erg) > 0 ) {
		
		while($row = db_fetch_assoc($erg) ) {
			if ($row['stat'] == 0) {
        $row['ORD'] = '<i rel="tooltip" title="Thema geschlossen" class="fa fa-lock colorclose" aria-hidden="true"></i>';
        $row['colorclosetopics'] = 'colorclosetopics';

			} else {
			  #$row['ORD'] = get_ordner($row['time']);
			  $row['ORD'] = forum_get_ordner($row['time'],$row['id'],$fid);
			  $row['colorclosetopics']  = '';
      }
      $times = $row['time'];
  if(date("d.m.Y",$times) == date("d.m.Y")) 
   {
   $row['date'] = "<b style=\"color:#ff0000;\">Heute</b> ".date("H:i",$times)." Uhr";        
   } 
elseif (date("d.m.Y",$times) == date("d.m.Y",time()-60*60*24))
   {
   $row['date'] = "Gestern ".date("H:i",$times)." Uhr";
   }
elseif (date("d.m.Y",$times) == date("d.m.Y",time()-60*60*48))
   {
   $row['date'] = "vor 2 Tagen ";
   }
else 
   {
   $row['date'] = "".date("d. M. Y",$times)."";
   }
			$ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['last'].'"'),0);
			$row['avatar']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="pull-left showforumavatar" src="'.$ergava.'" alt="Avatar" />' : '<img class="pull-left showforumavatar" src="include/images/avatars/wurstegal.jpg" />';
			$ergava2 = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['erst'].'"'),0);
			$row['avatar2']  = (!empty($ergava2) AND file_exists($ergava2)) ? '<img class="pull-left showforumavatar2" src="'.$ergava2.'" alt="Avatar" />' : '<img class="pull-left showforumavatar2" src="include/images/avatars/wurstegal.jpg" />';		
			$row['page'] = ceil ( ($row['rep']+1)  / $allgAr['Fpanz'] );
			$row['VORT'] = ( $row['art'] == 1 ? '<i rel="tooltip" title="Hot Thema" class="fa fa-thermometer-full festthema" aria-hidden="true"></i>' : '' );
			if($row['ORD'] == '<i rel="tooltip" title="Neuer Beitrag" class="fa fa-plus-circle colorplus" aria-hidden="true"></i>') 
   {
   $row['colorclosetopics2'] = 'colorclosetopics2';
   } 
else 
   {
   $row['colorclosetopics2']  = '';
   }
		  $tpl->set_ar_out($row,1);

	}   } else {
	   echo '<div class="alert alert-info">keine Eintr&auml;ge vorhanden</div>';
		}
    
    
$tpl->out(2);
if ( $forum_rights['mods'] == TRUE ) {
  $tpl->set('id', $fid);
  $tpl->out(3);
}
    
    
 
$design->footer();
?>