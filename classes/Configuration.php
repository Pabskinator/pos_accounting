<?php
	class Configuration extends Crud{
		protected $_table = 'configurations';

		public function __construct($c=null){
			parent::__construct($c);
		}

		public function getConfig($cid){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;

				$q= "select name,value from configurations where company_id = ?";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					$arr = [];
					foreach( $data->results() as $ind) {
						$arr[$ind->name] = $ind->value;
					}
					$_SESSION['configurations'] =  $arr;
				}
				return false;
			}
		}
		public static function getMainBranch(){
			$http_host = $_SERVER['HTTP_HOST'];
			$id =0;
			
			if(Configuration::thisCompany('pw')){
				$id = 1;
			} else if($http_host == 'kababayan.apollosystems.com.ph'){
				$id = 0;
			} else if($http_host == 'vitalite.apollosystems.com.ph'){
				$id = 0;
			} else if($http_host == 'calayan.apollosystems.com.ph'){
				$id = 0;

			} else if($http_host == 'aquabest.apollosystems.com.ph'){
				$id = 0;
			}else if($http_host == 'cebuhiq.apollosystems.com.ph'){
				$id = 0;
			} else if($http_host == 'dev.apollo.ph:81'){
				$id = 1;
				//$id = 27;
			}
			return $id;
		}
		public static function getSpecialItem(){

			$arr = [];
			if(Configuration::thisCompany('pw')){
				$arr = [121];
			}

			return $arr;
		}
		public static function getSuperAdminId(){
			$http_host = $_SERVER['HTTP_HOST'];
			$id =0;
			if(Configuration::thisCompany('pw')){
				$id = 1;
			} else if($http_host == 'kababayan.apollosystems.com.ph'){
				$id = 1;
			} else if(Configuration::thisCompany('vitalite')){
				$id = 110;
			} else if(Configuration::thisCompany('calayan')){
				$id = 1;

			} else if(Configuration::thisCompany('aquabest')){
				$id = 1;
			} else if($http_host == 'dev.apollo.ph:81'){

				$id = 1;
			}
			return $id;
		}

		public static function getSpecificRack(){
			$http_host = $_SERVER['HTTP_HOST'];
			$arr = [];
			if($http_host == 'aquabest.apollosystems.com.ph'){
				//$arr[2] = "22";
			} else if($http_host == 'dev.apollo.ph:81'){
				$arr[28] = "1593";
			}
			return $arr;
		}

		public static function getValue($name =''){
			if($name){
				if(isset($_SESSION['configurations'][$name])){
					return $_SESSION['configurations'][$name];
				} else {
					return '';
				}
			}
			return '';
		}
		public function updateValue($n,$v,$c){
			$parameters = array();
			if($c && $n) {
				$parameters[] = $v;
				$parameters[] = $n;
				$parameters[] = $c;

				$q = "update configurations set `value` = ? where `name`=? and company_id=?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
				return false;
			}
		}
		public function configExists($v,$c){
			$parameters = array();
			if($v && $c) {
				$parameters[] = $v;
				$parameters[] = $c;

				$q = "Select `name` from configurations where `name`=? and company_id=?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
				return false;
			}
		}
		public static function allowedPermission($perc =''){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			if(Configuration::thisCompany('pw')){
				$allowedPerm = ['sms','consume_supply','med'];
			} else if($http_host == 'kababayan.apollosystems.com.ph'){
				$allowedPerm = ['consume_supply','point_system'];
			} else if(Configuration::thisCompany('vitalite')){
				$allowedPerm = ['consume_supply','vit'];
			} else if($http_host == 'aquabest.apollosystems.com.ph'){
				$allowedPerm = ['consume_supply','equipment'];
			} else if($http_host == 'sh.apollosystems.com.ph'){
				$allowedPerm = ['consume_supply','gym'];
			} else if(Configuration::thisCompany('calayan')){
				$allowedPerm = ['consume_supply','med'];
			} else if($http_host == 'bgcon.apollosystems.com.ph'){
				$allowedPerm = ['consume_supply','item_post'];
			} else if($http_host == 'demo.apollosystems.com.ph'){
				$allowedPerm = ['consume_supply','item_post'];
			} else if($http_host == 'dev.apollo.ph:81'){
				$allowedPerm = ['sms','consume_supply','med','point_system','gym','item_post','equipment','vit'];
			} else if($http_host == 'localhost:81'){
				$allowedPerm = ['sms','consume_supply','med','point_system','gym','item_post','equipment','vit'];
			} else if($http_host == 'localhost'){
				$allowedPerm = ['sms','consume_supply','med','equipment','vit'];
			} else {
				$allowedPerm = ['consume_supply'];
			}

			if(in_array($perc,$allowedPerm)) return true;
			else return false;

		}


		public static function isGym(){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$res = false;
			if($http_host == 'safehouse.apollosystems.ph' || $http_host == 'sh.apollosystems.com.ph'){
				$res = true;
			}else if($http_host == 'dev.apollo.ph:81'){
				$res = true;
			} else if($http_host == 'localhost:81'){
				$res = true;
			} else if($http_host == 'localhost'){
				$res = true;
			}
			return $res;
		}
		public static function isSalon(){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$res = false;
			if($http_host == 'cn.apollosystems.com.ph'){
				$res = true;
			} else	if($http_host == 'demo.apollosystems.com.ph' || $http_host == 'apollosystems.com.ph' || $http_host == 'apollosystems.net'){
				$res = true;
			} else	if($http_host == 'zenspa.apollosystems.com.ph'){
				$res = true;
			}else if($http_host == 'dev.apollo.ph:81'){
				$res = true;
			} else if($http_host == 'localhost'){
				$res = true;
			}
			return $res;
		}
		public static function isAquabest(){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$res = false;
			if($http_host == 'dev.apollo.ph:81'){
				$res = true;
			} else if($http_host == 'localhost:81'){

			} else if($http_host == 'localhost'){
				$res = true;
			} else if(Configuration::thisCompany('aquabest')){
				$res = true;
			}
				return $res;
		}

		public static function companyName(){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$name = '';
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$sublink = substr($actual_link,0,31);

			$sublinknet = substr($actual_link,0,33);
			if(($http_host == 'cebuhiq.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/ce'))){
				$name = 'cebuhiq';
			} else if (($http_host == 'aquabest.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/aq'))){
				$name = 'aquabest';
			} else if (($http_host == 'vitalite.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/vi'))){
				$name = 'vitalite';
			} else if (($http_host == 'pw.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/pw'))){
				$name = 'pw';
			} else if (($http_host == 'calayan.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/mp')))
			{
				$name = 'calayan';
			} else if (($http_host == 'avision.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/avision')))
			{
				$name = 'avision';
			}else if (($http_host == 'zenspa.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/zenspa')))
			{
				$name = 'zenspa';
			}else if (($http_host == 'zamaryan.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/zamarya')))
			{
				$name = 'zamaryan';

			}

			if($http_host == 'localhost:81'){
				$name = 'avision';
			}

			return $name;
		}
		public static function thisCompany($c_name){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$res = false;
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$sublink = substr($actual_link,0,31);

			$sublinknet = substr($actual_link,0,33);

			if(($http_host == 'cebuhiq.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/ce')) && $c_name == 'cebuhiq'){
				$res = true;
			} else if (($http_host == 'aquabest.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/aq')) && $c_name == 'aquabest'){
				$res = true;
			} else if (($http_host == 'vitalite.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/vi')) && $c_name == 'vitalite'){
				$res = true;
			} else if (($http_host == 'pw.apollosystems.com.ph' || ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/pw')) && $c_name == 'pw'){
				$res = true;
			} else if (($http_host == 'calayan.apollosystems.com.ph'  ||  ($http_host == 'apollosystems.com.ph' && $sublink == 'https://apollosystems.com.ph/mp')) && $c_name == 'calayan')
			{
				$res = true;
			} else if (($http_host == 'avision.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/avision')) && $c_name == 'avision')
			{
				$res = true;
			}else if (($http_host == 'zenspa.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/zenspa')) && $c_name == 'zenspa')
			{
				$res = true;
			}else if (($http_host == 'zamaryan.apollosystems.net'  ||  ($http_host == 'apollosystems.net' && $sublinknet == 'https://apollosystems.net/zamarya')) && $c_name == 'zamaryan')
			{
				$res = true;
			}

			$skip  = [];
			if($http_host == 'localhost:81'){
					if(!in_array($c_name,$skip))
						$res = true;
			}

			return $res;
		}

		public static function getFolderName($c){
			$folder = "";

			if($c == 'cebuhiq'){
				$folder = "cebuhiq";
 			} else if ($c == 'pw'){
				$folder = "pw";
			} else if ($c == 'calayan'){
				$folder = "mp";
			}
			return $folder;
		}

		public static function showWarranty(){
			// sms, sub_company
			$http_host = $_SERVER['HTTP_HOST'];
			$res = false;
			if($http_host == 'aquabest.apollosystems.com.ph'){
				$res = true;
			 }else if($http_host == 'dev.apollo.ph:81'){
				$res = true;
			}else if($http_host == 'localhost:81'){
				$res = true;
			}

			return $res;
		}

	}
?>