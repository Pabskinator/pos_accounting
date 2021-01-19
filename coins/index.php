<?php
	include '../library/Requests.php';
	include 'Coins.php';
	Requests::register_autoloader();
	function dump ($var, $label = 'Dump', $echo = TRUE)
	{
		// Store dump in variable
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		// Add formatting
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		$output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';
		// Output
		if ($echo == TRUE) {
			echo $output;
		}
		else {
			return $output;
		}
	}
	$API_KEY = 'rZCbkVxb8WLxKHbrVMgwNFgLB9PgsJtFwz9pj0cX';
	$API_SECRET = 'MH35ZZVRqt8xKOvFDYWpnO2NCG9tTzyI5NSNlFe9m4ArmNWlY2';
	$TOKEN = $_GET['access_token'];
	//$coins = Coins::withHMAC('ID', 'KEY')
	//$coins = Coins::withOAuthToken($TOKEN);
	//$target_address= "iloveprogramming17@gmail.com";
	//$response = $coins->sendBitcoin($target_address, $amount);
	/* $response = Requests::get('https://coins.ph/api/v3/payment-requests/5edc263fac7f4f61b87632cb5710050f/',
		array(
			'Authorization' => "Bearer " . $TOKEN,
			'Content-Type'=> 'application/json;charset=UTF-8',
            'Accept'=> 'application/json'
		)); */

	$response = Requests::get('https://coins.ph/api/v3/transfers',
		array(
			'Authorization' => "Bearer " . $TOKEN,
			'Accept' => 'application/json'
		));
	dump($response->body);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src='../js/jquery.js'></script>
</head>
<body>
<?php echo $TOKEN; ?>
<input type="hidden" id='API_KEY' value='<?php echo $API_KEY; ?>'>
<input type="hidden" id='API_SECRET' value='<?php echo $API_SECRET; ?>'>
<input type="hidden" id='API_SECRET' value='<?php echo $TOKEN; ?>'>


<script>
	$(function(){

	})
</script>
</body>
</html>