<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$uid = intval($menu->get(2));

$abf = 'SELECT * FROM prefix_user WHERE id = "'.$uid.'"';
$erg = db_query($abf);

if (db_num_rows($erg)) {
	$row = db_fetch_assoc($erg);

	$avatar = '';
	if ( file_exists($row['avatar'])) {
		$avatar = '<img src="'.$row['avatar'].'" border="0">';
	}
else {
		$avatar = '<img src="include/images/avatars/wurstegal.jpg" border="0">';
}
if ($row['opt_mail'] == 1) {
  $usermail .= '<a rel="tooltip" title="Email scheiben" class="btn btn-primary" href="index.php?user-mail-'.$uid.'"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>';
} else {
  $usermail .= '';
}
if ($row['opt_pm'] == 1) {
  $userpm .= '<a rel="tooltip" title="PM scheiben" class="btn btn-primary" href="index.php?forum-privmsg-new=0&amp;empfid='.$uid.'"><i class="fa fa-comments-o" aria-hidden="true"></i></a>';
} else {
  $userpm .= '';
}
if ($row['status'] == 1) {
  $userstatus .= '<button rel="tooltip" title="'.$row['name'].' ist aktiv" type="button" class="btn btn-success"><i class="fa fa-battery-full" aria-hidden="true"></i></button>';
} else {
  $userstatus .= '<button rel="tooltip" title="'.$row['name'].' ist inaktiv" type="button" class="btn btn-danger"><i class="fa fa-battery-empty" aria-hidden="true"></i></button>';
}
if ($row['staat']) {
  $userstaat .= '<img class="userflags" src="include/images/flags/'.$row['staat'].'">';
} else {
  $userstaat .= '';
}

	$regsek = mktime ( 0,0,0, date('m'), date('d'), date('Y') )  - $row['regist'];
	$regday = round($regsek / 86400);
	$postpday = ( $regday == 0 ? 0 : round($row['posts'] / $regday, 2 ) );

	$ar = array (
	  'NAME' => $row['name'],
		'JOINED'  => date('d M Y',$row['regist']),
		'LASTAK'  => date('d M Y - H:i',$row['llogin']),
		'POSTS'   => $row['posts'],
		'postpday' => $postpday,
		'RANG'    => userrang ($row['posts'],$uid),
		'AVATA'   => $avatar,
    'WWW'   => '<a rel="tooltip" title="Webseite" class="btn btn-primary" href="'.$row['homepage'].'" target="_blank"><i class="fa fa-globe" aria-hidden="true"></i></a>',
    'USERMAIL'   => $usermail,
    'USERPM'   => $userpm,
    'STAAT'   => $userstaat,
    'STATUS'   => $userstatus,
);

	$title = $allgAr['title'].' :: Users :: Details von '.$row['name'];
	$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Details von '.$row['name'].$extented_forum_menu_sufix;
	$design = new design ( $title , $hmenu, 1);
	$design->header();

	$tpl = new tpl ( 'user/userdetails' );

	$l = profilefields_show ( $uid );

	$ar['rowspan'] = 4 + substr_count($l, '<tr><td class="');

	$ar['profilefields'] = $l;
	$tpl->set_ar_out($ar,0);
} else {
	$title = $allgAr['title'].' :: Users :: User nicht gefunden';
	$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?user">Users</a> '.$extented_forum_menu_sufix;
	$design = new design ( $title , $hmenu, 1);
	$design->header();

	echo '<div class="alert alert-warning" role="alert">Der Benutzer wurde nicht gefunden bzw. die Seite wurde nicht richtig aufgerufen.</div>';
}

$design->footer();
?>