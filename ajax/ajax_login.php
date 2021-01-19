<?php
	include 'ajax_connection.php';
	require '../classes/class.phpmailer.php';
	require '../classes/class.smtp.php';


	// getting the captcha
	if($_SESSION['log_failed_attempts'] > 5){


	$captcha = "";
	if (isset($_POST["g_recaptcha_response"]))
		$captcha = $_POST["g_recaptcha_response"];

	if (!$captcha){
		die(0);
	}
	// handling the captcha and checking if it's ok


	$secret = "6LdSTw4UAAAAAJJ1QfkZiN112g2ERQw3HE2mHa_o";
	$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);


	}


	$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
	if(!function_exists('getOS')){
		function getOS() {

			global $http_user_agent;

			$os_platform  = "Unknown OS Platform";

			$os_array     = array(
				'/windows nt 10/i'      =>  'Windows 10',
				'/windows nt 6.3/i'     =>  'Windows 8.1',
				'/windows nt 6.2/i'     =>  'Windows 8',
				'/windows nt 6.1/i'     =>  'Windows 7',
				'/windows nt 6.0/i'     =>  'Windows Vista',
				'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
				'/windows nt 5.1/i'     =>  'Windows XP',
				'/windows xp/i'         =>  'Windows XP',
				'/windows nt 5.0/i'     =>  'Windows 2000',
				'/windows me/i'         =>  'Windows ME',
				'/win98/i'              =>  'Windows 98',
				'/win95/i'              =>  'Windows 95',
				'/win16/i'              =>  'Windows 3.11',
				'/macintosh|mac os x/i' =>  'Mac OS X',
				'/mac_powerpc/i'        =>  'Mac OS 9',
				'/linux/i'              =>  'Linux',
				'/ubuntu/i'             =>  'Ubuntu',
				'/iphone/i'             =>  'iPhone',
				'/ipod/i'               =>  'iPod',
				'/ipad/i'               =>  'iPad',
				'/android/i'            =>  'Android',
				'/blackberry/i'         =>  'BlackBerry',
				'/webos/i'              =>  'Mobile'
			);

			foreach ($os_array as $regex => $value)
				if (preg_match($regex, $http_user_agent))
					$os_platform = $value;

			return $os_platform;
		}
	}

	if(!function_exists('getBrowser')){
		function getBrowser() {

			global $http_user_agent;

			$browser        = "Unknown Browser";

			$browser_array = array(
				'/msie/i'      => 'Internet Explorer',
				'/firefox/i'   => 'Firefox',
				'/safari/i'    => 'Safari',
				'/chrome/i'    => 'Chrome',
				'/edge/i'      => 'Edge',
				'/opera/i'     => 'Opera',
				'/netscape/i'  => 'Netscape',
				'/maxthon/i'   => 'Maxthon',
				'/konqueror/i' => 'Konqueror',
				'/mobile/i'    => 'Handheld Browser'
			);

			foreach ($browser_array as $regex => $value)
				if (preg_match($regex, $http_user_agent))
					$browser = $value;

			return $browser;
		}
	}
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
	if ($response["success"] != false || $_SESSION['log_failed_attempts'] <= 5) {

		$un = Input::get("username");
		$pw = Input::get("password");
		$u = new User();



		$user_ip_addr = getRealIpAddr();

		$user_os        = getOS();

		$user_browser   = getBrowser();

		if($user_os == 'Unknown OS Platform'){
			$email_arr[] = "jayson.temporas@gmail.com";
			$res_mail  = sendMail(
				"iloveprogramming17@gmail.com",
				"Login Attempts Unknown OS",
				$email_arr,
				"Unknown OS",
				"Unknown OS $http_host IP $user_ip_addr",
				"",
				""
			);
		}

		if($user_browser == 'Unknown Browser'){
			$email_arr[] = "jayson.temporas@gmail.com";
			$res_mail  = sendMail(
				"iloveprogramming17@gmail.com",
				"Login Attempts Unknown Browser",
				$email_arr,
				"Unknown Browser",
				"Unknown Browser $http_host IP $user_ip_addr",
				"",
				""
			);
		}

		if($u->login($un,$pw)){
			$data = $u->getUsers($u->data()->company_id,$u->data()->id);
			$u->insertLoginAttempts($user_ip_addr,1,'',$http_user_agent,$user_os,$user_browser);
			$_SESSION['log_failed_attempts']  = 0;
			echo json_encode($data);
		}else {
			$tried = "Username: $un Password: $pw";
			$u->insertLoginAttempts($user_ip_addr,0,$tried,$http_user_agent,$user_os,$user_browser);
			$f = $u->tooManyLoginAttempts($user_ip_addr);
			$_SESSION['log_failed_attempts']  = $_SESSION['log_failed_attempts'] + 1;
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