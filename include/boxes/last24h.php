<?php 

defined ('main') or die ( 'no direct access' );
if (has_right(-1)) {
$sql= "SELECT name, id, recht,  llogin FROM `prefix_user`  WHERE llogin > UNIX_TIMESTAMP() - 86400 ORDER BY llogin DESC";  
echo '<div class="row"><div class="col-md-12"><div class="com10newsinput">';
$erg = db_query($sql);
$ges_useronline = db_result(db_query("SELECT count(id) FROM `prefix_user`  WHERE llogin > UNIX_TIMESTAMP() - 86400 ORDER BY llogin"),0);
echo "<legend>Benutzer, die in den letzten 24 Stunden online waren <span class=\"badge\">$ges_useronline </span></legend>";
$userout = array();
while($row = db_fetch_object($erg)) {
$row->name = '<a href="?user-details-'. $row->id .'">'. $row->name .'</a>'; 
$userout[] = $row->name;
}
echo implode(", ", $userout);
echo '</div></div></div>';
}
?>