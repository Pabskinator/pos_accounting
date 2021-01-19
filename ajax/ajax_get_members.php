<?php
	include 'ajax_connection.php';


	$company = Input::get("company_id");
	$individual_member = Input::get('member_id');
	$mem = new Member();
	$members = $mem->getMembers($company,$individual_member);

	if($members){
		foreach($members as $m){
			$consumable_remarks = "";
			$mycon = $mem->getMyConsumableAmount($m->id);
			$chk = new Cheque();
			$bounce = $chk->getBounceCheck($m->id);
			$m->personal_address = utf8_encode($m->personal_address);
			$bounce = (($bounce->camount))? $bounce->camount  : 0;
			$m->amt = ($m->amt) ? $m->amt : 0;
			$m->amt = $m->amt - $bounce;

			if($mycon){
				foreach($mycon as $c){
					$notvalid = $mem->getNotYetValidCheque($c->payment_id);
					if($notvalid->cheque_amount){
						$m->amt = $m->amt - $notvalid->cheque_amount;
					}
					$consumable_remarks .= $c->remarks;
				}
			} else {
				$m->amt = '0';
			}
			$consumable_remarks = str_replace("'",'',$consumable_remarks);
			$consumable_remarks = str_replace('"','',$consumable_remarks);

			$m->consumable_remarks = $consumable_remarks;
			if(!$m->freebiesamount){
				$m->freebiesamount='0';
			}

		}
		echo json_encode($members );
	} else {
		echo '0';
	}


