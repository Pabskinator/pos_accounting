<?php
	include 'ajax_connection.php';

	$t = Input::get('t');
	$terminal = new Terminal();
	 $terminal->update(array(
		'is_assigned' => 1
		), $t);
		echo 1;


?>