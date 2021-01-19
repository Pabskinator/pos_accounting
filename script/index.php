<?php
$erp = mysql_connect('localhost','root','409186963@stephen');
mysql_select_db("test",$erp);

// Make sure data is UTF*, that way database can see accents and stuff
mysql_query("SET NAMES 'utf8'", $erp);
mysql_query("SET CHARACTER_SET 'utf8'", $erp);

if($_POST){
    mysql_query("INSERT INTO gos (ip, count) VALUES ($_SERVER[REMOTE_ADDR],1 ");
    header('Location: Scripts.rar');

}
echo "<form method='post'><input class='viewb' type='submit' value='bill'></form>";