<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$abf = "SELECT * FROM prefix_user WHERE id = ".$menu->get(2);
$erg = db_query($abf);
$DA_IS_WAS_FAUL = FALSE;
if ( @db_num_rows($erg) <> 1 ) {
  $DA_IS_WAS_FAUL = TRUE;
}
$row = db_fetch_assoc($erg);
if ( $row['opt_mail'] == 0 ) {
  $DA_IS_WAS_FAUL = TRUE;
}
if ( $DA_IS_WAS_FAUL === TRUE ) {
  header ( 'location: index.php?'.$allAr['smodul'] );
  exit();
}

$title = $allgAr['title'].' :: Users :: eMail an '.$row['name'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?user">Users</a><b> &raquo; </b> eMail an '.$row['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();


if ( ! array_key_exists('klicktime',$_SESSION) ) { 
  $_SESSION['klicktime'] = ''; 
}

# vars definieren
$_POST['email'] = ( isset($_POST['email']) ? trim($_POST['email']) : '' );
$_POST['bet'] = ( isset($_POST['bet']) ? trim($_POST['bet']) : '' );
$_POST['txt'] = ( isset($_POST['txt']) ? trim($_POST['txt']) : '' );

if ( empty($_POST['bet']) OR empty($_POST['email']) OR empty($_POST['txt']) OR $_SESSION['klicktime'] > (time() - 60) ) {
  
	if ( !empty($_POST['send']) ) {
	  $fehler = '';
		if ( $_SESSION['klicktime'] > (time() - 60) ) {
		  $fehler .= '<div class="alert alert-danger">Bitte nicht so schnell eMails Schreiben.</div>';
		}
		if ( trim($_POST['bet']) == '' ) {
		  $fehler .= '<div class="alert alert-danger">Bitte einen Betreff angeben.</div>';
		}
    if ( trim($_POST['email']) == '' ) {
		  $fehler .= '<div class="alert alert-danger">Bitte eine eMail angeben.</div>';
		}
		if ( trim($_POST['txt']) == '' ) {
		  $fehler .= '<div class="alert alert-danger">Bitte eine Nachricht angeben.</div>';
		}
	} else {
	  $fehler = '';
	}
	echo $fehler;
	
  
  ?>
<div class="com10newsinput"> 
	<form action="index.php?user-mail-<?php echo $menu->get(2) ?>" method="POST" class="form-horizontal" role="form">
<legend>eMail an Benutzer <?php echo $row['name']; ?></legend>
<div class="row">
  <div class="col-md-8">
<div class="form-group">
    <label class="col-sm-2 control-label">Betreff</label>
    <div class="col-sm-10">
      <input type="text" name="bet" class="form-control" value="<?php echo $_POST['bet']; ?>" placeholder="Betreff">
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">Deine eMail</label>
    <div class="col-sm-10">
      <input type="text" name="email" class="form-control" value="<?php echo $_POST['email']; ?>" placeholder="Deine email">
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">Nachricht</label>
    <div class="col-sm-10">
      <textarea cols="40" rows="10" class="form-control" name="txt" placeholder="Nachricht"><?php echo $_POST['txt']; ?></textarea>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10">
<input type="submit" class="btn btn-primary" name="send" value="<?php echo $lang['formsub']; ?>">
</div></div>
</div></div>
</form>
</div>
  <?php
} else {
  $_SESSION['klicktime'] = time();
	if ( 1 == $row['opt_mail'] ) {
    icmail ($row['email'],strip_tags($_POST['bet']),strip_tags($_POST['txt']),'SeitenKontakt <'.escape_for_email($_POST['email']).'>');
	  wd ('index.php?forum','Die e-Mail wurde erfolgreich versendet.');
	} else {
    header ( 'location: index.php?'.$allAr['smodul'] );
    exit();
  }
}


$design->footer();

?>