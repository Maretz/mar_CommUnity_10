<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


$title = $allgAr['title'].' :: Forum :: Private Nachrichten';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-privmsg">Private Nachrichten</a>'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();


if ( $allgAr['Fpmf'] != 1 ) {
  echo '<div class="alert alert-info"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Private Nachrichten wurden vom Administrator komplett gesperrt !';
  echo '<br><a class="btn btn-default" href="javascript:history.back(-1)">zur&uuml;ck</a></div>';
  $design->footer(1);
} elseif ( !loggedin() ) {
  echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> G&auml;ste d&uuml;rfen keine Privaten Nachrichten Verschicken!</div>';
  $tpl = new tpl ( 'user/login' );
  $tpl->set_out('WDLINK', 'index.php', 0);
  $design->footer(1);
} elseif ( db_result(db_query("SELECT opt_pm FROM prefix_user WHERE id = ".$_SESSION['authid']),0) == 0 ) {
  echo '<div class="alert alert-info">Im <a href="index.php?user-profil"><strong>Profil</strong></a> einstellen das du die PrivMsg Funktion nutzen m&ouml;chtest</div>';
  $design->footer(1);
}

$uum = $menu->get(2);
switch ( $uum ) {
case 'new' :
		  # neue pm schreiben und eintragen
      $show_formular = true;
      $txt = '';
      $bet = '';

      if (isset($_POST['sub'])) {
				$txt  = escape($_POST['txt'], 'textarea');
        $bet  = escape($_POST['bet'], 'string');
        $name = escape($_POST['name'], 'string');
        if (1 == db_result(db_query("SELECT count(*) FROM prefix_user WHERE name = BINARY '".$name."'"),0)) {
          $show_formular = false;
        } else {
          echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Dieser Empf&auml;nger konnte nicht gefunden werden</div>';
        }
      }


      if ($show_formular === true) {
        $name = '';
        $empfid = 0;
        if (isset($_REQUEST['empfid'])) {
          $empfid = escape($_REQUEST['empfid'],'integer');
        }
        $empfid = escape($empfid, 'integer');
        if ($empfid > 0) {
          $name = db_result(db_query("SELECT name FROM prefix_user WHERE id = ".$empfid),0);
        }
        $ar = array (
				  'name'    => $name,
		      'SMILIES' => getsmilies(),
          'TXT'     => $txt,
          'BET'     => $bet,
				);

				if (isset($_REQUEST['text'])) {
          $ar['TXT'] = unescape(escape($_REQUEST['text'], 'textarea'));
        }
        if (isset($_REQUEST['anhang'])) {
          $x = explode("\n", unescape(escape(urldecode($_REQUEST['anhang']), 'textarea')));
          $n = '';
          for ($i=0; $i<=count($x); $i++) {
            if (empty($x[$i])) { continue; }
            $n .= '> '.$x[$i]."\n";
          }
          $ar['TXT'] .= "\n\n".$n;
        }
				if (isset($_POST['bet'])) {
          $ar['BET'] = unescape(escape($_REQUEST['bet'], 'string'));
        }
        if (isset($_POST['re']) AND strpos ($ar['BET'],'re') === FALSE AND strpos ($ar['BET'],'Re') === FALSE AND strpos ($ar['BET'],'RE') === FALSE) {
          $ar['BET'] = 'Re(1): '.$ar['BET'];
        } elseif (isset($_POST['re'])) {
          $x = preg_replace("/re\((\d+)\):.*/i", "\\1", trim($ar['BET']));
          if (is_numeric($x)) {
            $x = $x+1;
            $ar['BET'] = preg_replace("/(re)\(\d+\):(.*)/i", "\\1(".$x."):\\2", $ar['BET']);
          }
        }

				$tpl = new tpl ( 'forum/pm/new' );
		    $tpl->set_ar_out($ar,0);
      } else {
        $eid  = db_result(db_query("SELECT id FROM prefix_user WHERE name = BINARY '".$name."'"),0);
				sendpm($_SESSION['authid'], $eid, $bet, $txt);
		    wd('index.php?forum-privmsg','Die Nachricht wurde erfolgreich gesendet');
      }
  break;
case 'showmsg' :
		  # message anzeigen lassen
		  $pid = escape($menu->get(3), 'integer');
      $soeid = ($menu->get(4) == 's' ? 'eid' : 'sid' );
      $erg = db_query("SELECT a.gelesen, a.eid, a.sid, a.id, b.name, a.titel, a.time, a.txt FROM `prefix_pm` a LEFT JOIN prefix_user b ON a.".$soeid." = b.id WHERE a.id = ".$pid);
		  $row = db_fetch_assoc($erg);
      if (($row['sid'] <> $_SESSION['authid'] AND $menu->get(4) == 's')
       OR ($row['eid'] <> $_SESSION['authid'] AND $menu->get(4) != 's')) {
         $design->footer(1); }
		  if ($row['gelesen'] == 0 AND $menu->get(4) != 's') {
		    db_query("UPDATE `prefix_pm` SET gelesen = 1 WHERE id = ".$pid);
		  }
      $ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['name'].'"'),0);
      $row['avatar']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="showforumavatar pull-left" src="'.$ergava.'" alt="Avatar" />' : '<img class="showforumavatar pull-left" src="include/images/avatars/wurstegal.jpg" />';
		  $row['time'] = date('d M. Y - H:i',$row['time']);
			$row['anhang'] = urlencode($row['txt']);
			$row['txt'] = bbcode(unescape($row['txt']));
			if ($menu->get(4) == 's') {
        $tpl = new tpl ('forum/pm/show_mess_send');
      } else {
        $tpl = new tpl ( 'forum/pm/show_mess' );
      }
			$tpl->set_ar_out($row,0);
  break;
case 'delete' :
		  # löschen von nachrichten
      if ( $menu->get(3) != '' AND $menu->get(4) == '') { $_POST['delids'][] = $menu->get(3); }
   elseif ($menu->get(3) != '' AND $menu->get(4) == 's') { $_POST['delsids'][] = $menu->get(3); }
      if ( empty($_POST['delids']) AND empty($_POST['delsids'])) {
	      echo '<div class="alert alert-info">Es wurde keine Nachricht zum l&ouml;schen gew&auml;hlt.<br>';
		    echo '<a class="btn btn-primary" href="javascript:history.back(-1)">zur&uuml;ck</a></div>';
      } else {
        if ( (empty($_POST['delids']) AND empty($_POST['delsids'])) OR empty($_POST['sub']) ) {

					$delids = (empty($_POST['delids'])?$_POST['delsids']:$_POST['delids']);
					$s = (empty($_POST['delids'])?'':'s');
					echo '<form action="index.php?forum-privmsg-delete" method="POST">';
			 	  $i = 0;
				  if ( !is_array($delids) ) { $delids = array ($delids); }
				  foreach ($delids as $a) {
				    $i++;
					  echo '<input type="hidden" name="del'.$s.'ids[]" value="'.$a.'">';
				  }
				  echo '<br><div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Wollen Sie ';
				  echo ($i > 1 ? 'die ('.$i.') Nachrichten ' : 'die Nachricht ' );
					echo 'wirklich löschen ?</p><p><input type="submit" class="btn btn-danger" value="Löschen" name="sub">  <input type="button" class="btn btn-primery" value="Nein" onclick="document.location.href =\'?forum-privmsg\'"></p></div></form>';

			  } else {
					$delids = (empty($_POST['delids'])?$_POST['delsids']:$_POST['delids']);
					$s = (empty($_POST['delids'])?'':'s');
					$soeid = ($s == 's'? 'sid' : 'eid');
          $stat1 = ($s == 's'? 1 : -1);
          $stat2 = $stat1 * -1;
			    $i = 0;
				  if ( !is_array($delids) ) {
				    $delids = Array ($delids);
				  }
				  foreach ($delids as $a) {
            if ( is_numeric($a) AND $a <> 0) {
              db_query("DELETE FROM `prefix_pm` WHERE id = ".$a." AND ".$soeid." = ".$_SESSION['authid']." AND status = ".$stat1);
              db_query("UPDATE prefix_pm SET status = ".$stat2." WHERE id = ".$a." AND ".$soeid." = ".$_SESSION['authid']);
              $i++;
            }
				  }
				  echo '<br><div class="alert alert-success"><p>Es wurd';
				  echo ($i > 1 ? 'en ('.$i.') Nachrichten ' : 'e eine Nachricht ' );
					echo 'erfolgreich gel&ouml;scht.</p><p><a class="btn btn-default" href="index.php?forum-privmsg">zum Nachrichten Eingang</a></p></div>';
			  }
			}
  break;
case 'showsend' :
  $tpl = new tpl ( 'forum/pm/showsend' );
  $ad = $menu->getA(3) == 'a' ? 'ASC' : 'DESC';
  $tpl->set_out('ad',$ad == 'ASC'?'d':'a',0); $class = 'Cmite';
  switch ($menu->getE(3)) {
    default: case '3': $order = "a.time $ad"; break;
             case '2': $order = "b.name $ad, a.time DESC"; break;
             case '1': $order = "a.titel $ad, a.time DESC"; break;
  }
  $abf = "SELECT a.titel, b.name as empf, a.id, a.`time` FROM `prefix_pm` a left join prefix_user b ON a.eid = b.id WHERE a.sid = ".$_SESSION['authid']." AND a.status >= 0 ORDER BY $order";
  $erg = db_query($abf);
  while ($row = db_fetch_assoc($erg)) {
    $class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
    $ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['empf'].'"'),0);
    $row['avatar']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="showforumavatar" src="'.$ergava.'" alt="Avatar" />' : '<img class="showforumavatar" src="include/images/avatars/wurstegal.jpg" />';
	  $row['class'] = $class;
    $row['date'] = date('d.m.Y',$row['time']);
    $row['time'] = date('H:i',$row['time']);
    $row['BET'] = (trim($row['titel']) == '' ? ' <small class="text-muted">-- kein Nachrichtentitel --</small> ' : $row['titel']);
	$tpl->set_ar_out($row,1);
  }
  $tpl->out(2);
  break;
default :
		  # message übersicht.
      $tpl = new tpl ( 'forum/pm/show' );
      $ad = $menu->getA(2) == 'a' ? 'ASC' : 'DESC';
      $tpl->set_out('ad',$ad == 'ASC'?'d':'a',0); $class = 'Cmite';
      switch ($menu->getE(2)) {
        default: case '3': $order = "a.time $ad"; break;
                 case '2': $order = "b.name $ad, a.time DESC"; break;
                 case '1': $order = "a.titel $ad, a.time DESC"; break;
      }
      $abf = "SELECT a.titel as BET, a.gelesen as NEW, b.name as ABS, a.id as ID, a.`time` FROM `prefix_pm` a left join prefix_user b ON a.sid = b.id WHERE a.eid = ".$_SESSION['authid']." AND a.status <= 0 ORDER BY $order";
      $erg = db_query($abf);

      while ($row = db_fetch_assoc($erg)) {
        $ergava = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "'.$row['ABS'].'"'),0);
        $row['AVATAR']  = (!empty($ergava) AND file_exists($ergava)) ? '<img class="showforumavatar pull-left" src="'.$ergava.'" alt="Avatar" />' : '<img class="showforumavatar pull-left" src="include/images/avatars/wurstegal.jpg" />';
        $class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
        $row['NEW'] = ($row['NEW'] == 0 ? 'pmnew' : '' );
        $row['CLASS'] = $class;
        $row['BET'] = (trim($row['BET']) == '' ? ' <small class="text-muted">-- kein Nachrichtentitel --</small> ' : $row['BET']);
        $row['date'] = date('d.m.Y',$row['time']);
        $row['time'] = date('H:i',$row['time']);
        $tpl->set_ar_out($row,1);
      }
      $tpl->out(2);
  break;
}
$design->footer();
?>