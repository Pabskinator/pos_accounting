
<?php
	include 'ajax_connection.php';
	$cid = Input::get("cid");
	$type= Input::get("type");
	if($type==1){
	$branch = new Branch();
	$branches =  $branch->get_active('branches',array('company_id' ,'=',$cid));
		if($branches) {
			$selectbranch = "<select id='branches' class='form-control'>";
			$selectbranch .= "<option value=''>--Select Branch</option>";
			foreach($branches as $b) {
				$selectbranch .= "<option value='$b->id'>$b->name</option>";
			}
			$selectbranch .= "</select>";
			echo $selectbranch;
		} else {
			echo 1;
		}
	} else if($type==2){
	// get terminals by branch

	$terminal = new Terminal();
	$terminals = $terminal->getUnassignTerminal($cid);

	if(!$terminals){

		$selectterminal = "<select id='terminals' class='form-control'>";
		$selectterminal .= "<option value=''>--Select Terminal--</option>";
		$selectterminal .= "<option value='0,0'>None</option>";
		$selectterminal .= "</select>";
		echo $selectterminal;
		exit();
	}
		$selectterminal = "<select id='terminals' class='form-control'>";
		$selectterminal .= "<option value=''>--Select Terminal--</option>";
	foreach($terminals as $t){
		$selectterminal .= "<option value='$t->id,$t->invoice'>$t->name</option>";
	}
		$selectterminal .= "<option value=0,0'>None</option>";
		$selectterminal .= "</select>";
		echo $selectterminal;
	} else if ($type == 3){
		// get invoice
		$terminal = new Terminal();
		$inv = $terminal->getInvoice($cid);

		echo $inv->invoice.":"
			.$inv->end_invoice.":"
			.$inv->dr.":"
			.$inv->end_dr.":"
			.$inv->invoice_limit.":"
			.$inv->dr_limit.":"
			.$inv->ir.":"
			.$inv->end_ir.":"
			.$inv->ir_limit.":"
			.$inv->speed_opt.":"
			.$inv->use_printer.":"
			.$inv->data_sync.":"
			.$inv->news_print.":"
			.$inv->print_inv.":"
			.$inv->print_dr.":"
			.$inv->print_ir.":"
			.$inv->pref_inv.":"
			.$inv->pref_dr.":"
			.$inv->pref_ir.":"
			.$inv->suf_inv.":"
			.$inv->suf_dr.":"
			.$inv->suf_ir.":"
			.$inv->sv.":"
			.$inv->sv_limit.":"
			.$inv->suf_sv.":"
			.$inv->pref_sv . ":"
			.$inv->sr.":"
			.$inv->sr_limit.":"
			.$inv->suf_sr.":"
			.$inv->pref_sr . ":"
			.$inv->ts.":"
			.$inv->ts_limit.":"
			.$inv->suf_ts.":"
			.$inv->pref_ts;

	}else if($type==4){
		// get terminals by branch

		$qs = new Queu();
		$qss = $qs->getQueues($cid);

		if(!$qss){
			echo '0';
			exit();
		}

		$selectq = json_encode($qss);

		echo $selectq;
	} else if ($type==5){
		$branch = new Branch();
		$branches =  $branch->get_active('branches',array('company_id' ,'=',$cid));
		if($branches) {
			echo json_encode($branches);
		} else {
			echo 1;
		}
	}else if ($type == 6){
		// get terminals by branch

		$terminal = new Terminal();
		$terminals = $terminal->getAllTerminal($cid);

		if(!$terminals){

			$selectterminal = "<select id='terminals' class='form-control'>";
			$selectterminal .= "<option value=''>--Select Terminal--</option>";
			$selectterminal .= "<option value='0,0'>None</option>";
			$selectterminal .= "</select>";
			echo $selectterminal;
			exit();
	}
		$selectterminal = "<select id='terminals' class='form-control'>";
		$selectterminal .= "<option value=''>--Select Terminal--</option>";
		foreach($terminals as $t){
			$selectterminal .= "<option value='$t->id,$t->invoice'>$t->name</option>";
		}
		$selectterminal .= "<option value=0,0'>None</option>";
		$selectterminal .= "</select>";
		echo $selectterminal;
	} else if ($type == 7){
		 $terminal_id = Input::get('terminal_id');
		 $invoice = Input::get('invoice') + 1;
		 $dr = Input::get('dr') + 1;
		 $ir = Input::get('ir') + 1;

		$sales = new Sales();

		$has_invoice = $sales->isControlNumberExistsInTerminal($terminal_id,1,$invoice);
		$has_dr = $sales->isControlNumberExistsInTerminal($terminal_id,2,$dr);
		$has_ir = $sales->isControlNumberExistsInTerminal($terminal_id,3,$ir);
		$msg ="";
		$arrnext=[];
		if(isset($has_invoice->cnt) && $has_invoice->cnt > 0){
			$msg .= INVOICE_LABEL. " is already taken. ";
			$lastNumInvoice = $sales->lastNumInTerminal($terminal_id,1);
			$msg .= "Your next " .INVOICE_LABEL . " will be " .($lastNumInvoice->invoice + 1);
			$arrnext['invoice'] = $lastNumInvoice->invoice;
		}
		if(isset($has_dr->cnt) && $has_dr->cnt > 0){
			$msg .= DR_LABEL ." is already taken. ";
			$lastNumIDR = $sales->lastNumInTerminal($terminal_id,2);
			$msg .= "Your next " .DR_LABEL . " will be " .($lastNumIDR->dr + 1);
			$arrnext['dr'] = $lastNumIDR->dr;
		}
		if(isset($has_ir->cnt) && $has_ir->cnt > 0){
			$msg .= PR_LABEL. " is already taken. ";
			$lastNumPR = $sales->lastNumInTerminal($terminal_id,3);
			$msg .= "Your next " .PR_LABEL . " will be " .($lastNumPR->ir + 1);
			$arrnext['ir'] = $lastNumPR->ir;
		}


		$arr = [];

		if($msg){
			$arr['msg'] = $msg;
			$arr['next'] = $arrnext;
		}
		echo json_encode($arr);


	}
?>

