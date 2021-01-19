<?php 
	include 'connection.php';
	require '../../classes/class.phpmailer.php';
	require '../../classes/class.smtp.php';
	session_start();
	if(isset($_POST["functionName"])){
		$functionName = $_POST["functionName"];
	} else if (isset($_GET['functionName'])){
		$functionName = $_GET["functionName"];
	} else {
		die("Invalid access");
	}
	if(function_exists($functionName)){
		$functionName($mysqli);
	}
	function sendMail($fromEmail='',$fromName='',$email = [], $subject='', $body = '', $altbody ='',$replyTo=''){
		$mail = new PHPMailer;
		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.apollosystems.com.ph';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = '_mainaccount@apollosystems.com.ph';                 // SMTP username
		$mail->Password = '409186963@StephenWang';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 25;                                    // TCP port to connect to
		$mail->setFrom('_mainaccount@apollosystems.com.ph', $fromName);
		if(count($email) > 0){
			foreach($email as $e){
				$mail->addAddress($e, 'test ');
			}
		}

		// Add a recipient              // Name is optional
		$mail->addReplyTo($replyTo, '');

		// Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $altbody;

		if(!$mail->send()) {
			return false;
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			return true;
		}

	}

	function encrypt_decrypt($action, $string) {
			$output = false;

			$encrypt_method = "AES-256-CBC";
			$secret_key = 'pogisi';
			$secret_iv = 'jayson';

				// hash
			$key = hash('sha256', $secret_key);

				// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);

			if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
			}
			else if( $action == 'decrypt' ){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}
	function getCerf(){
		//Carregar imagem
		$name =  ucwords(strtolower(encrypt_decrypt('decrypt',$_GET['name'])));
		$award_details =ucwords(strtolower(encrypt_decrypt('decrypt',$_GET['item'])));
		$date = ucwords(strtolower(encrypt_decrypt('decrypt',$_GET['dt'])));
		$category_name = ucwords(strtolower(encrypt_decrypt('decrypt',$_GET['category_name'])));
		$extra_details = $category_name;
		header('Content-Disposition: Attachment;filename=' . str_replace(' ', '', $name) ."-". str_replace(' ', '', $award_details) .".png");
		header('Content-type: image/png');
		$rImg = ImageCreateFromJPEG("../img/cerf.jpg");

		//Definir cor
		$cor = imagecolorallocate($rImg, 0, 0, 0);

		//Escrever nome
		//imagestring($rImg,1000,500,500,urldecode("JAYSON TEMPORAS"),$cor);

		//Header e output
		//header('Content-type: image/jpeg');
		//imagejpeg($rImg,NULL,100);

		// Set the enviroment variable for GD


		// Name the font to be used (note the lack of the .ttf extension)


		// Create some colors
		$white = imagecolorallocate($rImg, 255, 255, 255);
		$grey = imagecolorallocate($rImg, 128, 128, 128);
		$black = imagecolorallocate($rImg, 0, 0, 0);



		// The text to draw
	
		// Replace path by your own font path
		$font = '../fonts/roboto/Roboto-Bold.ttf';
		// Get Bounding Box Size
		$name_box = imagettfbbox(25,0,$font,$name);
		$name_width = $name_box[2]-$name_box[0];
		$name_left = ((792 - $name_width) / 2) + 500;


		$award_box = imagettfbbox(15,0,$font,$award_details);
		$award_width = $award_box[2]-$award_box[0];
		$award_left = ((792 - $award_width) / 2) + 500;

		$extra_box = imagettfbbox(15,0,$font,$extra_details);
		$extra_width = $extra_box[2]-$extra_box[0];
		$extra_left = ((237 - $extra_width) / 2) + 830;

		// Add some shadow to the text
		imagettftext($rImg, 25, 0, $name_left, 650, $grey, $font, $name);
		imagettftext($rImg, 25, 0, $name_left, 650, $black, $font, $name);

		imagettftext($rImg, 15, 0, $award_left, 810, $grey, $font, $award_details );
		imagettftext($rImg, 15, 0, $award_left, 810, $black, $font, $award_details);

		imagettftext($rImg, 15, 0, $extra_left, 905, $grey, $font, $extra_details );
		imagettftext($rImg, 15, 0, $extra_left, 905, $black, $font, $extra_details );

		imagettftext($rImg, 15, 0, 580,990, $grey, $font, $date);
		imagettftext($rImg, 15, 0, 580, 990, $black, $font, $date);


		// Using imagepng() results in clearer text compared with imagejpeg()
		imagepng($rImg);

		exit;
	}

	function uploadAtt($mysqli){
		if (!empty($_FILES)) {

			$tempFile = $_FILES['file']['tmp_name'];          
			$targetPath = "../uploads/" ;
			$name = "att_" . uniqid() ;
			$path = $_FILES['file']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$targetFile =  $targetPath.$name . ".".$ext;  
			move_uploaded_file($tempFile,$targetFile); 
			$message =  $name . ".".$ext;
			$user_id_1 = $_POST['user_id_1'];
			$user_id_2 = $_POST['user_id_2'];
		
			
			$now = time();
			$is_file = 1;
			$q = "INSERT INTO 
			`chat_box`(`msg`, `user_id_from`, `user_id_to`, `is_read`, `is_active`, `is_file`, `created`, `company_id`)
			VALUES('$message',$user_id_1,$user_id_2,0,1,$is_file,$now,1)
			 ";

			$result = $mysqli->query($q);

			
		}
	}

	function getConversation($mysqli){
		$user_id =$_SESSION['user_id'];
		

		$q = "Select cb.created,cb.user_id_to, cb.msg, u1.lastname as lnfrom, u2.lastname  as lnto, u2.firstname  as fnto, u2.middlename  as mnto from chat_box cb left join users u1 on u1.id = cb.user_id_from left join users u2 on u2.id = cb.user_id_to
			 where cb.user_id_from = $user_id  group by cb.user_id_from, cb.user_id_to";

		$result = $mysqli->query($q);
	
		$arr = [];

		if($result->num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){ 
				$from = ucwords($row['lnfrom']);
				$from = str_replace('"', '', $from);
				$from = str_replace("'", '', $from);
				$from = utf8_encode($from);

				$to = ucwords($row['lnto'] . ", " . $row['fnto'] . " " . $row['mnto']);
				$to = str_replace('"', '', $to);
				$to = str_replace("'", '', $to);
				$to = utf8_encode($to);

				$arr[] = ['from' => $from ,'to' => $to , 'id' =>  $row['user_id_to']];
			
			}
		}

		$q = "Select cb.created, cb.user_id_from,cb.msg, u1.lastname as lnfrom,u1.firstname as fnfrom,u1.middlename as mnfrom, u2.lastname  as lnto from chat_box cb left join users u1 on u1.id = cb.user_id_from left join users u2 on u2.id = cb.user_id_to
			 where cb.user_id_to = $user_id  group by cb.user_id_from, cb.user_id_to";

		$result = $mysqli->query($q);
	
		
		if($result->num_rows > 0){

			while($row = $result->fetch_array(MYSQLI_ASSOC)){ 

				$from = ucwords($row['lnfrom'] . ", " . $row['fnfrom'] . " " . $row['mnfrom']);
				$from = str_replace('"', '', $from);
				$from = str_replace("'", '', $from);
				$from = utf8_encode($from);

				$to = ucwords($row['lnto']);
				$to = str_replace('"', '', $to);
				$to = str_replace("'", '', $to);
				$to = utf8_encode($to);
				$valid = true;
				foreach($arr as $a){
					if($a['from'] == $to){
						$valid = false;
						break;
					}
				}
				if($valid){
					$arr[] = ['from' => $to ,'to' => $from , 'id' =>  $row['user_id_from']];
		
				}
		
			}

		}

		echo json_encode($arr);
	}
	function getMessages($mysqli){
		$user_id_1 = $_POST['user_id_1'];
		$user_id_2 = $_POST['user_id_2'];

		$q = "Select cb.*, u.firstname , u.lastname from chat_box cb left join users u on u.id = user_id_from
			where 
			(cb.user_id_from = $user_id_1  and cb.user_id_to = $user_id_2)
			or
			(cb.user_id_to = $user_id_1  and cb.user_id_from = $user_id_2)
			 order by created asc limit 150";

		$result = $mysqli->query($q);
	
		$arr = [];
		if($result->num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){ 
				$name = ucwords($row['firstname'] . " " . $row['lastname']);
				$name = str_replace('"', '', $name);
				$name = str_replace("'", '', $name);
				$name = utf8_encode($name);
				if($row['is_file'] == 1){
					 $row['msg'] = "<a href='uploads/$row[msg]' target='_blank'><img src='uploads/$row[msg]'></a>";
				}
				$arr[] = ['is_file' => $row['is_file'],'name' => $name,'from' => $row['user_id_from'],'to' => $row['user_id_to'] ,'msg' => $row['msg'] ,'created' => date('m/d/Y H:i:s A',$row['created'])];
			
			}
		}
		echo json_encode($arr);
	}
	function insertMessage($mysqli){
		$user_id_1 = $_POST['user_id_1'];
		$user_id_2 = $_POST['user_id_2'];
		$message = $_POST['msg'];
		$is_file = 0;
		$now = time();
		$q = "INSERT INTO 
				`chat_box`(`msg`, `user_id_from`, `user_id_to`, `is_read`, `is_active`, `is_file`, `created`, `company_id`)
				VALUES('$message',$user_id_1,$user_id_2,0,1,$is_file,$now,1)
				 ";

		$result = $mysqli->query($q);

	}
	function getUsers($mysqli){
		
		$q = "Select * from users where is_active = 1 and id != $_SESSION[user_id] order by firstname asc";

		$result = $mysqli->query($q);

		$arr = [];

		if($result->num_rows > 0){

			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$name = ucwords($row['firstname'] . " " . $row['lastname']);
				$name = str_replace('"', '', $name);
				$name = str_replace("'", '', $name);
				$name = utf8_encode($name);
				$arr[] = ['name' => $name,'id' => $row['id']];

			}

		}

		
		echo json_encode($arr);
		
	}

	function getExperience($mysqli){
		$member_id = $_SESSION['member_id'];
		
		$q = "Select id,time_in,is_con from service_attendance where member_id = $member_id ";
		$result = $mysqli->query($q);
		$total_expi_from_attendance = 0;
		$graph_arr = [];
		if($result->num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				if($row['is_con'] == 1){
					$exp_value = 15;
				} else {
					$exp_value = 10;
				}
				$total_expi_from_attendance	+= $exp_value;	
				$dt  = date('m/d/Y',$row['time_in']);
				$graph_arr[] = ['y' => $dt, 'a' => $total_expi_from_attendance];
			}
		}

		$q = "Select id,exp, created from addtl_experience where member_id = $member_id ";
		$result = $mysqli->query($q);
	
		if($result->num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$exp_value	 = $row['exp'];
				$total_expi_from_attendance	+= $exp_value;	
				$dt  = date('m/d/Y',$row['created']);
				$graph_arr[] = ['y' => $dt, 'a' => $total_expi_from_attendance];
			}
		}

		$graph_arr = array_slice($graph_arr, -10); // last ten only for  graphs
		echo json_encode(['data' => $graph_arr,'total' => $total_expi_from_attendance]);
		


	}
	function getAvailableTime($mysqli){
		$class_id = addslashes($_POST['class_id']);
		$dt = addslashes($_POST['dt']);
		$type = addslashes($_POST['type']);

		$day_of_the_week = date('l',strtotime($dt));

		$q = "Select cs.*, ch.name as coach_name, cs.coach_id from class_schedules cs left join coaches ch on ch.id = cs.coach_id
					where
					cs.day_of_the_week = '$day_of_the_week'
					and cs.class_id = $class_id
					and cs.is_active = 1
					and cs.is_pt = $type order by cs.time_start asc
					";

		$result = $mysqli->query($q);

		if($result->num_rows > 0){

			$ret = "";
/*$q = "INSERT INTO `member_service_request`
			(`member_id`, `schedule_date`, `time_of_the_day`, `created`, `status`, `is_active`, `company_id`, `remarks`,`class_id`,`req_type`,`coach_id`)
					VALUES ('$member_id','$dt','$row[time_of_the_day]','$now',1,1,1,'$remarks','$class_id',$type,$row[coach_id])"; */
			$cur_date = strtotime($dt);
			$q2 = "Select * from  member_service_request where schedule_date='$cur_date' and req_type='$type' and class_id='$class_id' ";
			$result2 = $mysqli->query($q2);
			$arr_taken = [];
			while($row = $result2->fetch_array(MYSQLI_ASSOC)){
				$arr_taken[]= trim($row['time_of_the_day'] . $row['coach_id']);
			}
			$hasOne = false;
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$compare = trim($row['time_of_the_day'] . $row['coach_id']);
				if(in_array($compare,$arr_taken)){
					$dis = "disabled";
				} else {
					$dis = "";
					$hasOne = true;
				}
				$ex = explode('-',$row['time_of_the_day']);
				$hr_from = date('h:i A',strtotime($ex[0]));
				$hr_to = date('h:i A',strtotime($ex[1]));
				$ret .= "<option value='$row[id]' $dis>$hr_from - $hr_to ($row[coach_name])</option>";

			}
			if(!$hasOne) $ret = "";

			echo $ret;
		}

	}
	function getFull($mysqli){

		$dt_main = $_POST['dt'];

		$dt1 = strtotime(date('F Y',strtotime($dt_main)));
		$dt2 = strtotime(date('m/d/Y',$dt1) . "1 month -1 min");
		$month =(int) date('m',$dt1);
		$year =(int) date('Y',$dt1);

		$start = date('d',$dt1);
		$end = date('d',$dt2);

		$ctr = 100;
		$arr_full = [];
		$start = (int)$start;
		for($i = $start; $i<=$end; $i++){
			 $dt = date("$month/$i/$year");
			$ctr++;
			if($ctr == 100){
				exit();
			}

			$type = addslashes($_POST['type']);
			$class_id = addslashes($_POST['class_id']);
			$day_of_the_week = date('l',strtotime($dt));

			$q = "Select cs.*, ch.name as coach_name, cs.coach_id
					from class_schedules cs left join coaches ch on ch.id = cs.coach_id
					where
					cs.day_of_the_week = '$day_of_the_week'
					and cs.class_id = $class_id
					and cs.is_active = 1
					and cs.is_pt = $type order by cs.time_start asc
					";

			$result = $mysqli->query($q);
			$hasOne = false;
			$ret = "";
			if($result->num_rows > 0){


				$cur_date = strtotime($dt);
				$q2 = "Select * from  member_service_request where schedule_date='$cur_date' and req_type='$type' and class_id='$class_id'  ";
				$result2 = $mysqli->query($q2);
				$arr_taken = [];
				if($result2->num_rows > 0){
					while($row = $result2->fetch_array(MYSQLI_ASSOC)){
						$arr_taken[]= trim($row['time_of_the_day'] . $row['coach_id']);
					}
				}

				if($result->num_rows > 0){
					while($row = $result->fetch_array(MYSQLI_ASSOC)){
						$compare = trim($row['time_of_the_day'] . $row['coach_id']);
						if(in_array($compare,$arr_taken)){

						} else {
							$hasOne = true;
							$ex = explode('-',$row['time_of_the_day']);
							$hr_from = date('h:i A',strtotime($ex[0]));
							$hr_to = date('h:i A',strtotime($ex[1]));
							$temp = "$hr_from - $hr_to \n ($row[coach_name])";
							$arr_full[] = [
								'title' => $temp,
								'start' => date('Y-m-d',strtotime($dt)),
								'dt' => date('m/d/Y',strtotime($dt)),
								'time_id' => [$row['id']],


							];
						}

					}
				}

			}
			if(!$hasOne){
				$arr_full[] = ['title' => 'Fully booked', 'start' => date('Y-m-d',strtotime($dt)),'color' => '#FF0000'];
			} else {

			}
		}
		echo json_encode($arr_full);



	}
	function sendOrder($mysqli){
		$cart = json_decode($_POST['cart']);
		$name = addslashes($_POST['name']);
		$phone = addslashes($_POST['phone']);
		$email = addslashes($_POST['email']);
		$address = addslashes($_POST['address']);

		$walkin_data = ['name' => $name,'phone' => $phone,'email' => $email,'address' => $address];
		$data = json_encode($walkin_data);
		// insert wh order
		$now = time();
		 $q_order = "
			INSERT INTO `wh_orders`(`branch_id`, `status`, `created`, `is_active`, `company_id` ,`remarks`, `walkin_info`) 
			VALUES
			(1,1,$now,1,1,'Order From Website','$data')";
		$mysqli->query($q_order);

	    $last_id =  $mysqli->insert_id;
			
			/*
				$order_details->create(array(
					'wh_orders_id' => $lastItOrder,
					'item_id' => $item->item_id,
					'price_id' => $price->id,
					'qty' => $qty,
					'created' => $now,
					'modified' => $now,
					'price_adjustment' => $adj_amount,
					'company_id' => $user->data()->company_id,
					'is_active' => 1,
					'terms' => $terms,
					'member_adjustment' => $alladj
				));

			*/

		foreach($cart as $c){
			$q_price =  "Select id from prices where item_id = $c->item_id and effectivity <= $now order by effectivity desc limit 1";
			
				$priceq = $mysqli->query($q_price);
				$result_price = $priceq->fetch_array(MYSQLI_ASSOC);
				
				$q_details = "INSERT INTO `wh_order_details`(`wh_orders_id`, `item_id`, `price_id` ,`qty`, `created`, `modified`, `company_id`,  `is_active`) 
								VALUES ($last_id,$c->item_id,$result_price[id],$c->qty,$now,$now,1,1)";
				$mysqli->query($q_details);	
		}
		echo 1;

		// insert wh order _details



	}
	function sendBooking($mysqli){
			$name = addslashes($_POST['name']);
			$phone = addslashes($_POST['phone']);
			$email = addslashes($_POST['email']);
			$age = addslashes($_POST['age']);
		$class_name = addslashes($_POST['class_name']);


			if($name && $phone && $email && $age){
				$now = time();
				 $q = "INSERT into online_web_inquiry (`name`,`phone`,`email`,`age`,`created`,`is_active`,`class_name`)VALUES('$name','$phone','$email','$age',$now,1,'$class_name')";
				$result = $mysqli->query($q);
				echo 1;
			}
	}
	function submitSchedule($mysqli){

		$class_id = addslashes($_POST['class_id']);
		$dt = addslashes($_POST['dt']);
		$tm =  addslashes($_POST['tm']);
		$type =  addslashes($_POST['type']);
		$remarks = addslashes($_POST['remarks']);
		$member_id = $_SESSION['member_id'];
		$name = addslashes($_POST['name']);
		$email = addslashes($_POST['email']);
		$contact = addslashes($_POST['contact']);

		$name = ($name) ? $name : '';
		$email = ($email) ? $email : '';
		$contact = ($contact) ? $contact : '';

		if($class_id && $dt && $tm){

			$dt = strtotime($dt);
			$now = time();

			$cur = "Select * from class_schedules where id = $tm";
			$result =  $mysqli->query($cur);
			$row = $result->fetch_array(MYSQLI_ASSOC);

			 $q = "INSERT INTO `member_service_request`
			(`member_id`, `schedule_date`, `time_of_the_day`, `created`, `status`, `is_active`, `company_id`, `remarks`,`class_id`,`req_type`,`coach_id`,`name`,`contact_number`,`email`)
					VALUES ('$member_id','$dt','$row[time_of_the_day]','$now',1,1,1,'$remarks','$class_id',$type,$row[coach_id],'$name','$contact','$email')";
			$result = $mysqli->query($q);
			// email

			$dtdt = date('m/d/Y',$dt);
			$other_info = "<br>Booking Date: $dtdt $row[time_of_the_day]<br>";
			$html = "<html><head><style> p { font-size: 18px; }</style></head><body><div style='width:100%;'><div style='width:600px;margin:0 auto;'><div style='background:#212121;position: relative;height: 80px;'><h1 style='color:white;padding-top:15px;text-align: center;'>Safehouse Fight Academy</h1></div><div><p>Hi<span style='color:red;'> $name</span>! Hope you are doing well. We noticed that you just booked at Safehouse Fight Academy, and We wanted to personally thank you. </p><p>$other_info</p><p>Please wait our call to verify your booking.</p><p> Note: <br>This e-mail message (including attachments, if any) is intended for the use of the individual or the entity to whom it is addressed and may contain information that is privileged, proprietary, confidential and exempt from disclosure. If you are not the intended recipient, you are notified that any dissemination, distribution or copying of this communication is strictly prohibited. If you have received this communication in error, please notify the sender and delete this E-mail message immediately. </p><p> All the best,<br>Safehouse Fight Academy Team<br> </p></div></div></div></body></html>";
			$email_arr = [$email];
			$subject = "Booking Request at Safehouse";

			$res_mail  = sendMail(
				"safehouse.manila@gmail.com",
				"Safehouse Fight Academy",
				$email_arr,
				$subject,
				$html,
				"",
				"safehouse.manila@gmail.com"
			);

			echo 1;
			
		}
		
	}

	function login($mysqli){
		$username = addslashes($_POST['username']);
		$password = addslashes(md5($_POST['password']));

		if ($mysqli->connect_errno)
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$query = "SELECT * FROM users where username = '$username' and password = '$password'";
		$result = $mysqli->query($query);
		
		/*
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			echo $row['lastname'] . "<br>";
		} */
		if($result->num_rows > 0){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$_SESSION['user_id']  =$row['id'];
			$_SESSION['member_id']  =$row['member_id'];

			$query_member = "SELECT * FROM members where id = $row[member_id] limit 1 ";
			$result_member = $mysqli->query($query_member);
			$row_member = $result_member->fetch_array(MYSQLI_ASSOC);
			$_SESSION['member_name'] = $row_member['lastname'];
			$_SESSION['member_since'] = ($row_member['member_since']) ? date('M d, Y',$row_member['member_since']) : "Not indicated. We'll update it soon";
			echo "1";
		} else {
			echo "0";
		}
		
		$result->free();
		$mysqli->close();

	}

	function logout(){
		unset($_SESSION['user_id']);
		unset($_SESSION['member_id']);
		unset($_SESSION['member_name']);
		unset($_SESSION['member_since']);
		echo 1;

	}

	function getStats($mysqli){
		$member_id = $_SESSION['member_id'];
		$query = "Select * from body_measurements where member_id = $member_id order by created asc";
		$result = $mysqli->query($query);
		$weight_arr = [];
		$arm_arr = [];
		$thigh_arr = [];
		$calf_arr =[];
		$services_arr = [];
		$num_rows = $result->num_rows;

		if($num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$dt = date('m/d/Y',$row['created']);
				$weight_arr[] = ['y' => $dt, 'a' => $row['weight']];
				$arm_arr[] = ['y' => $dt, 'a' => $row['l_upperarm'], 'b' => $row['r_upperarm']];
				$thigh_arr[] = ['y' => $dt, 'a' => $row['l_mid_thigh'], 'b' => $row['r_mid_thigh']];
				$calf_arr[] = ['y' => $dt, 'a' => $row['l_calf'], 'b' => $row['r_calf']];
			}
		}
		
		 $q_services = "Select count(s.service_id) as cnt , ss.name as o_name from offered_services_history s left join offered_services ss on ss.id = s.service_id where s.member_id = $_SESSION[member_id] group by s.service_id";
		$result_service = $mysqli->query($q_services);
		$num_rows_service = $result_service->num_rows;

		if($num_rows_service > 0){
			while($rservice = $result_service->fetch_array(MYSQLI_ASSOC)){
				$services_arr[] = ['label' => $rservice['o_name'] , 'value' =>  $rservice['cnt']];
			}
		}

		echo json_encode(['weight' => $weight_arr,'arm' => $arm_arr ,'calf' => $calf_arr,'thigh' => $thigh_arr,'services' => $services_arr] );

	}
?>