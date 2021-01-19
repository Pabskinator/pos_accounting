<?php
	include 'ajax_connection.php';


	$functionName = Input::get("functionName");

	$functionName();

	function uploadPic(){


		$storeFolder = '../uploads/';

		if (!empty($_FILES)) {
			$id = Input::get('id');
			$tempFile = $_FILES['file']['tmp_name'];

			$targetPath =  $storeFolder ;
			$ext = explode('.', basename($_FILES['file']['name']));
			$file_extension = end($ext);
			$file_name = 'call-' . uniqid() . "." . $file_extension;
			$targetFile =  $targetPath. $file_name;
			$user = new User();
			$ref = 'call_log';
			$upload = new Upload();
			$now = time();
			$upload->create(array(
				'filename' =>$file_name,
				'ref_table' => $ref,
				'ref_id' => $id,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'created' => date('Y-m-d H:i:s')
			));

			move_uploaded_file($tempFile,$targetFile);

		}
	}