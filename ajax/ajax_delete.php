<?php
	include 'ajax_connection.php';
	// decrypt the id and get the post data
	$id = Encryption::encrypt_decrypt('decrypt',Input::get('id'));
	$table = Input::get('table');
	$user = new User();
	// get instance of databases
	$db = DB::getInstance();

	// delete child terminals if table is branch
	if($table=='branches'){
		$db->update('terminals',$id,array('is_active','=',1));
	}
	// delete the data

	Log::addLog($user->data()->id,$user->data()->company_id,"Delete from $table where id=$id",'admin/addproduct.php');

	if($db->update($table, $id ,array('is_active' => 0))){
		echo 'true';
	}


