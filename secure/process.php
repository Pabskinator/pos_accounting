<?php
	require_once('secured_connection.php');
	//aff_id:aff_id,username:username,password:password,total:total,security_code:security_code
	$aff_id = Input::get('aff_id');
	$id = Encryption::encrypt_decrypt('decrypt',$aff_id);
	if(is_numeric($id)){
		$affiliate = new Affiliate($id);
		$err = "Invalid credential.";
		if($affiliate){
			$username = Input::get('username');
			$password = Input::get('password');
			$total = Input::get('total');
			$security_code = Input::get('security_code');
			$check_user = new User();
			if(isset($affiliate->data()->security_code) && $security_code ==  $affiliate->data()->security_code){
				$valid = $check_user->login($username,$password);
				if($valid){
					$user = new User();
					$point = new Point();
					$member = new Member();
					if($user->data()->member_id){
						/*$active_points = $point->getActiveUserPoint($user->data()->member_id);
						$need_unit = "point";
						if($active_points){
							foreach($active_points as $p){
								$gain_points = $point->updateUserPoint($user->data()->member_id,$user,$total,0,$p->point_id,0,0);
								if($p->unit_name == $need_unit){
									$affiliate->deductPoints($id,$gain_points);
								}
							}
							echo "Transaction complete";
						} else {
							echo "No enrolled services!";
						}*/

						//  if supplementary
						// 2.5 % company, 2.5% member niya

						// if normal member
						// 2.5 % member  2.5 % company
						$wallet = new Wallet();
						$user_id = $user->data()->id;
						$amount_to_add = $total * 0.025;
						$amount_to_deduction =$total * 0.05;
						$wallet->updateUserWallet($user,$user_id,$amount_to_add,0,"Add wallet from affiliate store.");

						// add company wallet
						$walletForAffiliate = $wallet->getForAffilate();
						if(isset($walletForAffiliate->id)){
							$wallet->updateCompanyWallet($user,$walletForAffiliate->id,$amount_to_add,"Add wallet from affiliate store.");
						}
						$affiliate->deductPoints($id,$amount_to_deduction);
					} else {
						echo $err;
					}
				} else {
					echo $err;
				}
			} else {
				echo $err;
			}
		} else {
			echo $err;
		}
	} else {
		echo $err;
	}

