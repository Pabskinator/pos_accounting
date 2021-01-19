<?php
	include 'ajax_connection.php';
	require '../classes/class.phpmailer.php';
	require '../classes/class.smtp.php';


	// getting the captcha
	$captcha = "";
	if (isset($_POST["g_recaptcha_response"]))
		$captcha = $_POST["g_recaptcha_response"];

	if (!$captcha)
		echo "no captcha ";
	// handling the captcha and checking if it's ok
	$secret = "6LdSTw4UAAAAAJJ1QfkZiN112g2ERQw3HE2mHa_o";
	$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);
	$name = $_POST['name'];

	// functions
	if(!function_exists('getRealIpAddr')){
		function getRealIpAddr()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
			{
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
			{
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
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
	// if the captcha is cleared with google, send the mail and echo ok.
	if ($response["success"] != false) {

		$un = Input::get("username");
		$pw = Input::get("password");
		$u = new User();



		$user_ip_addr = getRealIpAddr();

		if($u->login($un,$pw)){
			$data = $u->getUsers($u->data()->company_id,$u->data()->id);
			$u->insertLoginAttempts($user_ip_addr,1,'');
			echo json_encode($data);
		}else {
			$tried = "Username: $un Password: $pw";
			$u->insertLoginAttempts($user_ip_addr,0,$tried);
			$f = $u->tooManyLoginAttempts($user_ip_addr);

			if(isset($f->failed_attempts) && $f->failed_attempts == 6){
				$email_arr[] = "jayson.temporas@gmail.com";
				$res_mail  = sendMail(
					"iloveprogramming17@gmail.com",
					"Login Attempts",
					$email_arr,
					"Too many login attempts",
					"$http_host IP $user_ip_addr",
					"",
					""
				);
			}
			echo 0;
		}
	} else {
		echo 0;
	}