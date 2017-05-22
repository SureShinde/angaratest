<?php
$db_host = 'localhost';
$db_name = 'angara_cdn_080613';
$db_user = 'root';
$db_pass = 'RockAngHit1#';
$temp_con = mysql_connect($db_host, $db_user, $db_pass) or die("Could not connect: " . mysql_error());
mysql_select_db($db_name);
?>
