<?php
	include 'ajax_connection.php';
$id = Input::get('id');
$p = Input::get('price');
$prod = new Product();
$myprice = $prod->getPrice($id);
if($p == $myprice->price){
	echo "true";
} else {
	echo "false";
}
?>