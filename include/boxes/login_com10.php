<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de
defined ('main') or die ( 'no direct access' );

$tpl = new tpl ( 'user/boxen_logincom10.htm' );

if ( loggedin() ) {
  
  if ( user_has_admin_right($menu,false) ) {
    $tpl->set ( 'ADMIN', '<li><a href="admin.php?admin"><i class="fa fa-lock userdropspan" aria-hidden="true"></i>'.$lang['adminarea'].'</a></li>' );
  } else {
    $tpl->set ( 'ADMIN', '' );
  }
  $posts = db_query('SELECT posts from `prefix_user` WHERE id= "' . $_SESSION['authid'].'"' );
  if($posts = mysql_fetch_row($posts))
      $posts = $posts[0];
      else
      $posts = 0;
      
      $galerie = db_query('SELECT count(id) from `prefix_usergallery` WHERE uid= "' . $_SESSION['authid'].'"' );
  if($galerie = mysql_fetch_row($galerie))
      $galerie = $galerie[0];
      else
      $galerie = 0;

$abf = 'SELECT id, name, avatar FROM prefix_user WHERE name = "'.$_SESSION['authname'].'"';
$erg = db_query($abf);
$row = db_fetch_object($erg);

if ( file_exists($row->avatar)) {
  $avatar = '<img class="userloginavatar" src="'.$row->avatar.'">';
}else{
$avatar = '<img class="userloginavatar" src="include/images/avatars/wurstegal.jpg">';
}


$q = "SELECT COUNT(DISTINCT a.id) FROM prefix_topics a
    LEFT JOIN prefix_forums b ON b.id = a.fid
    LEFT JOIN prefix_posts c ON c.tid = a.id
    LEFT JOIN prefix_user d ON c.erstid = d.id
    LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = b.view
    LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = b.reply
    LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = b.start
  WHERE (((b.view >= ".$_SESSION['authright']." AND b.view <= 0) OR
            (b.reply >= ".$_SESSION['authright']." AND b.reply <= 0) OR
            (b.start >= ".$_SESSION['authright']." AND b.start <= 0)) OR
            (vg.fid IS NOT NULL OR rg.fid IS NOT NULL OR sg.fid IS NOT NULL OR ".$_SESSION['authright']." = -9))
     AND c.time >= ". (time() - (3600 * 24 * 360)) ." AND c.time >= {$_SESSION['lastlogin']}
  ORDER BY c.time DESC";
        
      $lpost = db_query($q);
      if($lpost = mysql_fetch_row($lpost))
      $lpost = $lpost[0];
      else
      $lpost = 0;
      
      if ($lpost > '0') {
			$neuerpost = '<a class="btn btn-com10-2 hvr-pop" href="index.php?forum-augt" data-toggle="tooltip" data-placement="top" title="Neue Beitr‰ge"><i class="fa fa-clipboard  login_blink_beitrag" aria-hidden="true"><sup>'.$lpost.'</sup></i></a>';
		} else {
		  $neuerpost = '<a class="btn btn-com10-2 hvr-pop" href="index.php?forum-augt" data-toggle="tooltip" data-placement="top" title="Neue Beitr‰ge"><i class="fa fa-clipboard" aria-hidden="true"><sup>'.$lpost.'</sup></i></a>';
		}
		

	  if ( $allgAr['Fpmf'] == 1 ) {
		  $erg = db_query("SELECT COUNT(id) FROM `prefix_pm` WHERE gelesen = 0 AND status < 1 AND eid = ".$_SESSION['authid']);
			$check_pm = db_result($erg,0);
			if ($check_pm > '0') {
			$nachrichten_link = '<a class="btn btn-com10-1 hvr-pop" href="index.php?forum-privmsg" data-toggle="tooltip" data-placement="top" title="Nachrichten"><i class="fa fa-envelope-o login_blink_PM" aria-hidden="true"><sup>'.$check_pm.'</sub></i></a>';
		} else {
		  $nachrichten_link = '<a class="btn btn-com10-1 hvr-pop" href="index.php?forum-privmsg" data-toggle="tooltip" data-placement="top" title="Nachrichten"><i class="fa fa-envelope-o" aria-hidden="true"><sup>'.$check_pm.'</sup></i></a>';
		}
		}		
		
$tpl->set ( 'UGALLERY', '<a class="btn btn-com10-3 hvr-pop" href="index.php?user-usergallery-'.$_SESSION['authid'].'" data-toggle="tooltip" data-placement="top" title="Meine Gallery"><i class="fa fa-file-image-o" aria-hidden="true"><sup>'.$galerie.'</sup></i></a>');
$tpl->set ( 'PROFILANSICHT', '<a href="index.php?user-details-'.$_SESSION['authid'].'"><i class="fa fa-eye userdropspan" aria-hidden="true"></i>Profil ansehen</a>');
			

		$tpl->set ( 'SID' , session_id() );
		$tpl->set ( 'NACHRICHTEN' , $nachrichten_link );
		$tpl->set ( 'NEWBEITRAG' , $neuerpost );
		$tpl->set ( 'NAME', $_SESSION['authname'] );
		$tpl->set('POSTS', $posts);
		$tpl->set('LPOSTS', $lpost);
        $tpl->set ( 'AVATAR' , $avatar );
		
    $tpl->set ( 'POPUP', check_for_pm_popup() );
	$tpl->out (0);		
}

 
else {
  if (empty($_POST['login_name'])) { $_POST['login_name'] = 'Username'; }
	if (empty($_POST['login_pw'])) { $_POST['login_pw'] = '‰‰‰‰‰‰‰‰‰'; }
	$regist = '';
	$loginpan = '<a href="#" data-toggle="modal" data-target=".bs-example-modal"><strong>Anmelden</strong></a> oder <a href="index.php?user-regist"><strong>Registrieren</strong></a>';
	if ( $allgAr['forum_regist'] == 1 ) {
	  $regist = '<a href="index.php?user-regist">Regist.</a> <a href="index.php?user-remind">Passw. lost?</a>';
	}
	$tpl->set_ar_out ( array ( 'regist' => $regist, 'loginpan' => $loginpan, 'wdlink' => '?'.$allgAr['smodul'], 'PASS' => $_POST['login_pw'], 'NAME' => $_POST['login_name'] ) , 1 );
}
unset($tpl);
?>
