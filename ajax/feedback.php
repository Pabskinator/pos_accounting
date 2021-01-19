<?php
	include 'ajax_connection.php';
	/*$user = new User();
	$user_id = $user->data()->id;
	$name = $user->data()->firstname . " " . $user->data()->lastname;
	$http_host = $_SERVER['HTTP_HOST'];
	if($http_host != 'dev.apollo.ph:81'){
		session_start();
		$erp = mysql_connect('localhost','apollo29_peanut','409186963@StephenWang');
		mysql_select_db("apollo29_erp",$erp);

		// Make sure data is UTF*, that way database can see accents and stuff
		mysql_query("SET NAMES 'utf8'", $erp);
		mysql_query("SET CHARACTER_SET 'utf8'", $erp);
		mysql_query("SET time_zone='+8:00'");

	}
	$data = $_POST['data'];
	$data = json_decode($data);
	$message = $data[0]->Issue;
	$image = $data[1];
	$data = str_replace('data:image/png;base64,', '', $image);
	$data = str_replace(' ', '+', $data);
	$data = base64_decode($data);
	$source_img = imagecreatefromstring($data);
	$file = '../screens/'. uniqid() . '.png';
	if($http_host != 'dev.apollo.ph:81') mysql_query("INSERT INTO `pos_feedback`(`from_url`, `path`, `user_id`, `msg`,`user_name`) VALUES ('$http_host','$file',$user_id,'$message','$name')");
	$imageSave = imagejpeg($source_img, $file, 70);
	imagedestroy($source_img);

	$f = new Feedback();
	$f->create(array(
		'company_id' => $user->data()->company_id,
		'user_id' => $user->data()->id,
		'feedback' =>$message,
		'filename' =>$file,
		'created' =>time()
	));

	if($http_host != 'dev.apollo.ph:81'){
		if(isset($erp)){
			mysql_close($erp);
		}
	}*/
