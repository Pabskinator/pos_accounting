<?php
	/*
	  This is an API wrapper class for CoinsPH.
	  To run it, you need to install "Requests for PHP" library.
	  If you're using Composer to manage dependencies, you can add Requests with it.
	  {
		"require": {
			"rmccue/requests": ">=1.0"
		}
	  }
	  For more information refer to this link. https://github.com/rmccue/Requests
	*/
	class Coins
	{
		protected $access_token;
		protected $client_id;
		protected $client_secret;
		protected $is_hmac;
		protected $URL_SELL_ORDER = "https://coins.ph/api/v2/sellorder";
		protected $URL_SELL_QUOTE = "https://coins.ph/api/v2/sellquote";
		protected $URL_CRYPTO_PAYMENTS = "https://coins.ph/api/v3/crypto-payments/";
		protected $URL_CRYPTO_ACCOUNTS = "https://coins.ph/api/v3/crypto-accounts/";
		public function __construct() {}
		/*
		  Create a coins client using OAuth token
		*/
		public static function withOAuthToken($access_token) {
			$instance = new self();
			$instance->initOAuth($access_token);
			return $instance;
		}
		/*
		  Create a coins client using Client ID and Secret (HMAC)
		*/
		public static function withHMAC($client_id, $client_secret) {
			$instance = new self();
			$instance->initHMAC($client_id, $client_secret);
			return $instance;
		}
		/*
		  Query sell quote to retrieve cash out options
		*/
		public function sellQuote($params) {
			return $this->executeRequest(
				$this->URL_SELL_QUOTE,
				$params,
				$method="GET"
			);
		}
		/*
		  Create a send money order
		*/
		public function sendMoney($params) {
			return $this->executeRequest(
				$this->URL_SELL_ORDER,
				$params,
				$method="POST"
			);
		}
		/*
		  Send BTC via email or bitcoin address
		*/
		public function sendBitcoin($target_address, $amount, $account=NULL) {
			$params = array();
			$params['target_address'] = $target_address;
			$params['amount'] = $amount;
			if ($account == NULL) {
				$params['account'] = $this->getBTCCryptoAccount();
			} else {
				$params['account'] = $account;
			}
			return $this->executeRequest(
				$this->URL_CRYPTO_PAYMENTS,
				$params,
				$method="POST"
			);
		}
		/*
		  Get BTC Crypto account
		*/
		public function getBTCCryptoAccount() {
			$params = array("currency"=>"BTC");
			$response = $this->executeRequest(
				$this->URL_CRYPTO_ACCOUNTS,
				$params,
				$method="GET"
			);
			$responseObject = json_decode($response->body);
			return $responseObject->{'crypto-accounts'}[0]->id;
		}
		/*
		  Execute HTTP request to API endpoint
		*/
		protected function executeRequest($url, $params, $method="GET") {
			Requests::register_autoloader();
			if ($method == "GET"){
				$url = $url."?".http_build_query($params);
			}
			if ($this->is_hmac) {
				if ($method == "POST") {
					$params = json_encode($params);
				}
				$headers = $this->createHMACRequestHeaders(
					$url, $params, $method
				);
			} else {
				$headers = $this->createOAuthRequestHeaders();
			}
			try {
				if ($method == "GET") {
					return Requests::get($url, $headers);
				} else {
					return Requests::post($url, $headers, $params);
				}
			} catch (Requests_Exception $e){
				print_r($e);
			}
		}
		protected function createOAuthRequestHeaders() {
			$nonce = intVal(round(microtime(true) * 1000));
			return array(
				'Authorization' => sprintf("Bearer %s", $this->access_token),
				'ACCESS_NONCE' => $nonce,
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Accept' => 'application/json'
			);
		}
		/*
		  Create request headers needed for HMAC / OAuth
		*/
		protected function createHMACRequestHeaders($url, $params, $method) {
			$nonce = intVal(round(microtime(true) * 1000));
			if ($method == "GET") {
				$message = sprintf("%d%s", $nonce, $url);
			} else {
				$body = $params;
				$message = sprintf("%d%s%s", $nonce, $url, $body);
			}
			$signature = hash_hmac('sha256', $message, $this->client_secret);
			if ($method == "GET") {
				return array(
					'ACCESS_SIGNATURE' => $signature,
					'ACCESS_KEY' => $this->client_id,
					'ACCESS_NONCE' => $nonce,
					'Accept' => 'application/json'
				);
			} else {
				return array(
					'ACCESS_SIGNATURE' => $signature,
					'ACCESS_KEY' => $this->client_id,
					'ACCESS_NONCE' => $nonce,
					'Content-Type' => 'application/json',
					'Accept' => 'application/json'
				);
			}
		}
		/*
		  Init instance access token
		*/
		protected function initOAuth($access_token) {
			$this->access_token = $access_token;
			$this->is_hmac = False;
		}
		/*
		  Init instance HMAC credentials
		*/
		protected function initHMAC($client_id, $client_secret) {
			$this->client_id = $client_id;
			$this->client_secret = $client_secret;
			$this->is_hmac = True;
		}
	}
?>