<?php 
defined ('main') or die ( 'no direct access' );

$abf = 'SELECT
          a.news_kat as kate,
          DATE_FORMAT(a.news_time,"%d.%m.%Y") as datum,      
          a.news_title as title,
          a.news_kat as kate,
          a.news_id as id,      
          b.name as username,
          b.id as userid         
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= '.$_SESSION['authright'].'
          ORDER BY a.news_time DESC
          LIMIT 0,5';
echo '<div class="newnewboxout"><table class="table table-forum newnewbox">'; 
echo '<div><i class="fa fa-newspaper-o" aria-hidden="true"></i> Letzte News</div>';  
$erg = db_query($abf);
if (loggedin()) {
    $admin = '';
    if (user_has_admin_right($menu, false)) {
        $admin = '<a href="admin.php?news">jetzt eine News erstellen</a>';
    }
}
if ( @db_num_rows($erg) == 0 ) {
	echo '<tr><td class="nonewstext"><span>kein Newseintrag vorhanden</span><br>'.$admin.'</td></tr>';
}
else { 
while ($row = db_fetch_object($erg)) {
		if ( file_exists( 'include/images/news/'.$row->kate.'.jpg' ) ) {
		  $row->katen = 'include/images/news/'.$row->kate.'.jpg';
		} elseif ( file_exists ( 'include/images/news/'.$row->kate.'.gif' ) ) {
		  $row->katen = 'include/images/news/'.$row->kate.'.gif';
		} elseif ( file_exists ( 'include/images/news/'.$row->kate.'.png') ) {
		  $row->katen = 'include/images/news/'.$row->kate.'.png';
		} elseif  (file_not_exists) {
		  $row->katen = 'include/images/news/noimage.png';
		}
	echo '<tr>';
	echo '<td style="vertical-align:middle;min-width:60px;"><img src="'.$row->katen.'" alt=""></td>';
	echo '<td><span>Kategorie: '.$row->kate.'</span><br><a href="index.php?news-'.$row->id.'">'.$row->title.'</a><br><span>Autor : '.$row->username.' # '.$row->datum.'</span></td>';
	echo '</tr>';
}
}
echo '</table></div>';
?>