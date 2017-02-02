<?php
defined('main') or die('no direct access');
$comAbf = "SELECT * FROM `prefix_koms` WHERE cat LIKE 'NEWS' ORDER BY id DESC LIMIT 0,7";
$comErg = db_query($comAbf);

if (db_num_rows($comErg) > 0)
	{
	echo '<div class="newnewboxout newscommenttop"><table class="table table-forum newnewboxforum">';
	echo '<div><i class="fa fa-comments-o" aria-hidden="true"></i> Letzte Kommentare</div>';
	while ($comRow = db_fetch_object($comErg))
		{
		$link = 'index.php?news-' . $comRow->uid;
		$name = $comRow->name;
		$comavatar = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $name . '"') , 0);
		$text = bbcode($comRow->text);
		$avatar = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="" src="' . $comavatar . '" alt="Avatar" />' : '<img class="" src="include/images/avatars/wurstegal.jpg" />';
		echo '<tr>';
		echo '<td class="commentboxavatar" style="vertical-align:middle;min-width:60px;">' . $avatar . '</td>';
		echo '<td class="commentboxtext">' . $text . '';
		echo '<br/><span>von ' . $name . '</span><span class="pull-right"><a title="zum Artikel" href="' . $link . '"><i class="fa fa-folder-open" aria-hidden="true"></i></a></span></td>';
		echo '</tr>';
		}

	echo '</table></div>';
	}
  else
	{
	echo '<div class="newnewboxout newscommenttop text-center">';
	echo '<div><i class="fa fa-comments-o" aria-hidden="true"></i> Letzte Kommentare</div>';
	echo 'Kein Kommentar vorhanden';
	echo '</div>';
	}

?>