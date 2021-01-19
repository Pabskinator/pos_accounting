<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();

	function getTime($time){
	/*	$lbl = "ago";
		if($time < 60){
			//seconds
			$res= ($time>1) ?  "secs $lbl" : "sec $lbl";
			return floor($time) . " $res";
		} else if ($time < (60*60)) {
			//minutes
			$del = number_format($time/60,2);
			$res = (floor($del) > 1) ? "mins $lbl" : "min $lbl";
			return floor($del) . " $res";
		} else if ($time < (60*60) * 24){
			// hrs
			$del = number_format(($time/60)/60,2);
			$res = (floor($del) > 1) ? "hrs $lbl" : "hr $lbl";
			return  floor($del). " $res";
		} else{
			// if ($time < (((60 *60) * 24) * 30))
			$del = number_format((($time/60)/60)/24,2);
			$res = (floor($del) > 1) ? "days $lbl" : "day $lbl";
			return  floor($del). " $res";
		} */
		$lbl = "ago";
		if($time < 60){
			//seconds
			return 0;
		} else if ($time < (60*60)) {
			//minutes

			return 0;
		} else if ($time < (60*60) * 24){
			// hrs
			return 0;
		} else{
			// if ($time < (((60 *60) * 24) * 30))
			$del = ((($time/60)/60)/24);
			$res = (floor($del) > 1) ? "days $lbl" : "day $lbl";
			return  floor($del);
		}
	}


function stsDownload(){

		$dt = Input::get('dt');
		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->getSalesTypeSummary($user->data()->company_id,$dt);
		$sales_type = new Sales_type();
		$sales_types = $sales_type->get_active('salestypes',['company_id','=',$user->data()->company_id]);

		$results = [];

		if($data){

			foreach($data as $d){
				$results[$d->sales_type][$d->m] = $d->totalamount;
			}

		}

		echo "<h3>Sales Type</h3>";

		echo "<table class='table table-bordered'>";
		echo "<tr>";
		echo "<th></th>";
		for($i=1;$i<=12;$i++){
			$monthNum  = $i;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('M');
			echo "<th  class='text-right' >$monthName</th>";
		}
		echo "<th></th>";
		echo "</tr>";
		$monthly = [];

		foreach($sales_types as $st){
			$st_id  = $st->id;
			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>$st->name</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$st_id][$i]) ? $results[$st_id][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;
				$total = number_format($total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		}
			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>No sales type</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results['0'][$i]) ? $results['0'][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;
				$total = number_format($total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		echo "<tr>";
		echo "<th  style='border-top: 1px solid #ccc;'>Total</th>";
		$total_monthly= 0;
		for($i=1;$i<=12;$i++){
			$total_monthly  += $monthly[$i];
			echo "<th   style='border-top: 1px solid #ccc;' class='text-right' >". number_format($monthly[$i],2)."</th>";
		}
		echo "<th  style='border-top: 1px solid #ccc;' class='text-right'>". number_format($total_monthly,2)."</th>";
		echo "</tr>";
		echo "</table>";


	}


	function sts(){
		$dt = Input::get('dt');
		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->getSalesTypeSummary($user->data()->company_id,$dt);
		$sales_type = new Sales_type();
		$sales_types = $sales_type->get_active('salestypes',['company_id','=',$user->data()->company_id]);

		$data_branch = $sales->getBranchSummary($user->data()->company_id,$dt);
		$data_service = $sales->getServiceSummary($user->data()->company_id,$dt);

		$results = [];
		if($data){
			foreach($data as $d){
				$results[$d->sales_type][$d->m] = $d->totalamount;
			}
		}

		echo "<h3>Sales Type</h3>";
		echo "<table class='table table-bordered'>";
		echo "<tr>";
		echo "<th></th>";
		for($i=1;$i<=12;$i++){
			$monthNum  = $i;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('M');
			echo "<th  class='text-right' >$monthName</th>";
		}
		echo "<th></th>";
		echo "</tr>";
		$monthly = [];

		foreach($sales_types as $st){
			$st_id  = $st->id;
			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>$st->name</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$st_id][$i]) ? $results[$st_id][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;
				$total = number_format($total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		}
			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>No sales type</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results['0'][$i]) ? $results['0'][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;
				$total = number_format($total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		echo "<tr>";
		echo "<th  style='border-top: 1px solid #ccc;'>Total</th>";
		$total_monthly= 0;
		for($i=1;$i<=12;$i++){
			$total_monthly  += $monthly[$i];
			echo "<th   style='border-top: 1px solid #ccc;' class='text-right' >". number_format($monthly[$i],2)."</th>";
		}
		echo "<th  style='border-top: 1px solid #ccc;' class='text-right'>". number_format($total_monthly,2)."</th>";
		echo "</tr>";
		echo "</table>";
		echo "<hr><h3>Branch</h3>";
		echo "<table class='table table-bordered'>";
		echo "<tr>";
		echo "<th></th>";
		for($i=1;$i<=12;$i++){
			$monthNum  = $i;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('M');
			echo "<th  class='text-right' >$monthName</th>";
		}
		echo "<th></th>";
		echo "</tr>";
		$monthly = [];
		$results = [];
		$all_bc = [];
		if($data_branch){
			foreach($data_branch as $d){
				$all_bc[$d->branch_id] = $d->branch_name;
				$results[$d->branch_id][$d->m] = $d->totalamount;
			}
		}
		$service_monthly = [];
		$total_monthly_service = [];
		if($data_service){
			foreach($data_service as $d){
				if(!isset($total_monthly_service[$d->m])){
					$total_monthly_service[$d->m] = $d->totalamount;
				} else {
					$total_monthly_service[$d->m] += $d->totalamount;
				}

				$service_monthly[$d->branch_id][$d->m] = $d->totalamount;
			}
		}
		foreach($all_bc as $branch_id => $branch_name){
			$st_id  = $branch_id;
			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>$branch_name</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$deduct_service =  isset($service_monthly[$st_id][$i]) ? $service_monthly[$st_id][$i] : 0;
				$total= isset($results[$st_id][$i]) ? $results[$st_id][$i] : 0;

				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$cur_total = $total - $deduct_service;
				$monthly[$i] += $cur_total;
				$gtotal += $cur_total;
				$cur_total = number_format($cur_total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$cur_total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		}

		echo "<tr>";
		echo "<td  style='border-top: 1px solid #ccc;'>Service</td>";
		$total_service= 0;
		for($i=1;$i<=12;$i++){
			$cur = isset($total_monthly_service[$i]) ? $total_monthly_service[$i] : 0;
			$total_service  += $cur;
			echo "<td   style='border-top: 1px solid #ccc;' class='text-right' >". number_format($cur,2)."</td>";
		}
		echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>". number_format($total_service,2)."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<th  style='border-top: 1px solid #ccc;'>Total</th>";
		$total_monthly= 0;
		for($i=1;$i<=12;$i++){
			$service_i = isset($total_monthly_service[$i]) ? $total_monthly_service[$i] : 0;
			$overall_monthly = $monthly[$i] + $service_i;
			$total_monthly  += $overall_monthly;
			echo "<th   style='border-top: 1px solid #ccc;' class='text-right' >". number_format($overall_monthly,2)."</th>";
		}
		echo "<th  style='border-top: 1px solid #ccc;' class='text-right'>". number_format($total_monthly,2)."</th>";
		echo "</tr>";
		echo "</table>";

	}

	function regionSummary(){
		$dt = Input::get('dt');
		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->getRegionSummary($user->data()->company_id,$dt);


		$results = [];
		$region_list = [];
		if($data){
			foreach($data as $d){
				$results[$d->region_name][$d->m] = $d->totalamount;
				if(!in_array($d->region_name,$region_list)){
					$region_list[] = $d->region_name;
				}
			}
		}


		echo "<table class='table table-bordered'>";
		echo "<tr>";
		echo "<th></th>";
		for($i=1;$i<=12;$i++){
			$monthNum  = $i;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('M');
			echo "<th  class='text-right' >$monthName</th>";
		}
		echo "<th></th>";
		echo "</tr>";
		$monthly = [];

		foreach($region_list as $region){

			echo "<tr>";
			echo "<td style='border-top: 1px solid #ccc;'>$region</td>";
			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$region][$i]) ? $results[$region][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;
				$total = number_format($total,2);
				echo "<td class='text-right' style='border-top: 1px solid #ccc;'>$total</td>";
			}
			echo "<td  style='border-top: 1px solid #ccc;' class='text-right'>".number_format($gtotal,2)."</td>";
			echo "</tr>";
		}
		echo "</tr>";
		echo "<tr>";
		echo "<th  style='border-top: 1px solid #ccc;'>Total</th>";
		$total_monthly= 0;
		for($i=1;$i<=12;$i++){
			$total_monthly  += $monthly[$i];
			echo "<th   style='border-top: 1px solid #ccc;' class='text-right' >". number_format($monthly[$i],2)."</th>";
		}
		echo "<th  style='border-top: 1px solid #ccc;' class='text-right'>". number_format($total_monthly,2)."</th>";
		echo "</tr>";
		echo "</table>";


	}

	function regionOA(){
		$user = new User();
		$sales = new Sales();
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');



		$list = $sales->getRegionOA($user->data()->company_id,$dt1,$dt2);

		if($list){

			echo "<p>From: ".$dt1." To: ".$dt2."</p>";

			echo "<table class='table table-bordered'>";
			echo "<thead><tr><th>Area/Region</th><th>Total</th></tr></thead>";
			foreach($list as $l){
			$region_name = $l->region_name;
			if($l->region_name === null){
				$region_name = "No client";
			}
			if($l->region_name === ''){
				$region_name = "Member without area";
			}
			echo "<tr><td style='border-top:1px solid #ccc;'>$region_name</td><td class='text-right' style='border-top:1px solid #ccc;'>".number_format($l->totalamount,2)."</td></tr>";
			}
			echo "</table>";
		}

	}

	function salesSummary(){
	echo "<p class='text-danger'>* Under construction!</p>";
		$branch_id = Input::get('branch_id');
		$user = new User();
		$sales = new Sales();


		if($branch_id != -1){
			$data = $sales->getBranchSummaryYearly($user->data()->company_id,$branch_id);
			$data_service = $sales->getServiceSummaryYearly($user->data()->company_id,$branch_id);
		} else {
			$branch_id = 0;
			$data_service = $sales->getServiceSummaryYearly($user->data()->company_id,$branch_id);
			$data = $data_service;
			$data_service = [];
		}


		if($data){
			$arr = [];
			$arr_service = [];
			$years = [];
			if($data){
				foreach($data as $d){
				 if(!in_array($d->y,$years)) $years[] =  $d->y ;
					$arr[$d->m][$d->y] =  $d->totalamount;
				}
			}
			if($data_service){
				foreach($data_service as $d){
				 if(!in_array($d->y,$years)) $years[] =  $d->y ;
					$arr_service[$d->m][$d->y] =  $d->totalamount;
				}
			}


			asort($years);

			echo "<table class='table table-bordered table-condensed' id='tblWithBorder'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>Month</th>";
			foreach($years as $y){
				if(!$y) continue;
				echo "<th class='text-right'>$y</th>";
			}
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			for($i=1;$i<=12;$i++){
				if(!isset($arr[$i])){
					$arr[$i] = [];
				}

			}
			ksort($arr);
			$total_year = [];
			foreach($arr as $m => $item){
				if(!$m) continue;
				echo "<tr>";
				$monthNum  = $m;
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('M');
				echo "<td>$monthName</td>";
				foreach($years as $year){
					if(!$year) continue;
					$cur = isset($item[$year]) ? $item[$year] : 0;
					$service_to_deduct =   isset($arr_service[$m][$year]) ? $arr_service[$m][$year] : 0;
					$cur = $cur - $service_to_deduct;
					if(isset($total_year[$year]))
						$total_year[$year] += $cur;
					else
						$total_year[$year] = $cur;

					echo "<td class='text-right'>".number_format($cur,2)."</td>";
				}
				echo "</tr>";
			}

			echo "</tbody>";
			echo "<tfoot>";
			echo "<tr><th>Total: </th>";
			foreach($years as $year){
					if(isset($total_year[$year]))
						$total = $total_year[$year];
					else
						$total = 0;

					echo "<th class='text-right'>".number_format($total,2)."</th>";
			}
			echo "</tr>";
			echo "</tfoot>";
			echo "</table>";


		} else {
			echo "<div class='alert alert-info'>No Record Found</div>";
		}
	}
	function topClient(){
		$dt = Input::get('dt');
		if(!$dt){
			$dt = date('m') . "-" .date('Y');
		}

		$explode = explode('-',$dt);
		$monthNum  = $explode[0];
		$dateObj   = DateTime::createFromFormat('!m', $monthNum);
		$monthName = $dateObj->format('F');
		$dt = $monthName . " 1, " . $explode[1];
		$dt1 = strtotime($dt);
		$dt2 = strtotime($dt . " 1 month -1 sec");

		$user = new User();
		$sales = new Sales();
		$data = $sales->topClientSales($user->data()->company_id,$dt1,$dt2);

		if($data){
			echo "<table class='table table-bordered' id='tblWithBorder'>";
			echo "<tr><th>#</th><th>Sales Type</th><th>Client</th><th class='text-right'>Total</th></tr>";
			$i = 1;
			foreach($data as $item){
				if(!$item->member_name) continue;
				echo "<tr><td>$i</td><td>".$item->sales_type_name."</td><td>".$item->member_name."</td><td class='text-right'>".number_format($item->saletotal,2)."</td></tr>";
				$i++;
			}
			echo "</table>";
		} else {

		}
	}
	function dss(){
			$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		if($dt1 && $dt2){
			$dt1= strtotime($dt1);
			$dt2= strtotime($dt2 . "1 day -1 sec");
		} else {
			$dt1 = strotitme(date('F Y'));
			$dt2 = strtotime(date('F Y') . "1 month -1 sec");
		}

		$sales = new Sales();
		$user = new User();

		$list = $sales->getAll($dt1,$dt2);
		$arr = [];
		if(!$list){
			echo "<div class='alert alert-info'>No Record found</div>";
			exit();
		}
		foreach($list as $l){

			if($l->invoice) $arr[$l->terminal_name]['invoice'][] = $l->invoice;
			if($l->dr)  $arr[$l->terminal_name]['dr'][] = $l->dr;
			if($l->ir)  $arr[$l->terminal_name]['ir'][] = $l->ir;

			if($l->status == 1){
				if($l->invoice){
					$arr[$l->terminal_name]['invoice_ctr']['cancel'] = isset($arr[$l->terminal_name]['invoice_ctr']['cancel']) ? ($arr[$l->terminal_name]['invoice_ctr']['cancel'] + 1) : 1;
				}
				if($l->dr){
					$arr[$l->terminal_name]['dr_ctr']['cancel'] = isset($arr[$l->terminal_name]['dr_ctr']['cancel']) ? ($arr[$l->terminal_name]['dr_ctr']['cancel'] + 1) : 1;
				}
				if($l->ir){
					$arr[$l->terminal_name]['ir_ctr']['cancel'] = isset($arr[$l->terminal_name]['ir_ctr']['cancel']) ? ($arr[$l->terminal_name]['ir_ctr']['cancel'] + 1) : 1;
				}

			} else {

				if($l->invoice){
					$arr[$l->terminal_name]['invoice_ctr']['used'] = isset($arr[$l->terminal_name]['invoice_ctr']['used']) ? ($arr[$l->terminal_name]['invoice_ctr']['used'] + 1) : 1;
				}
				if($l->dr){
					$arr[$l->terminal_name]['dr_ctr']['used'] = isset($arr[$l->terminal_name]['dr_ctr']['used']) ? ($arr[$l->terminal_name]['dr_ctr']['used'] + 1) : 1;
				}
				if($l->ir){
					$arr[$l->terminal_name]['ir_ctr']['used'] = isset($arr[$l->terminal_name]['ir_ctr']['used']) ? ($arr[$l->terminal_name]['ir_ctr']['used'] + 1) : 1;
				}
			}
		}

		echo "<h4>".date('F d, Y',$dt1)." - ".date('F d, Y',$dt2)."</h4>";

		foreach($arr as $a => $c){


			echo "<table class='table table-bordered'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th colspan='5'>$a</th>";
			echo "</tr>";
			echo "<tr><th>Name</th><th>Min</th><th>Max</th><th>Cancelled</th><th>Used</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			echo "<tr>";
			echo "<td style='border-top:1px solid #ccc;'>".INVOICE_LABEL."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (min($c['invoice']) | 0) . "</td>";
			echo "<td style='border-top:1px solid #ccc;'>" .(max($c['invoice']) | 0) ."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['invoice_ctr']['cancelled']) ? $c['invoice_ctr']['cancelled'] : 0) ."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['invoice_ctr']['used']) ? $c['invoice_ctr']['used'] : 0) ."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td style='border-top:1px solid #ccc;'>".DR_LABEL."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" .(min($c['dr']) | 0)."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" .(max($c['dr'])| 0)."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['dr_ctr']['cancelled']) ? $c['dr_ctr']['cancelled'] : 0) ."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['dr_ctr']['used']) ? $c['dr_ctr']['used'] : 0) ."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td style='border-top:1px solid #ccc;'>".PR_LABEL."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" .(min($c['ir'])| 0)."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" .(max($c['ir'])| 0)."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['ir_ctr']['cancelled']) ? $c['ir_ctr']['cancelled'] : 0) ."</td>";
			echo "<td style='border-top:1px solid #ccc;'>" . (isset($c['ir_ctr']['used']) ? $c['ir_ctr']['used'] : 0) ."</td>";
			echo "</tr>";
			echo "</tbody>";
			echo "</table>";

		}




		$ret_html = "";


		echo $ret_html;

	}
	function ar() {
		// select distinct sales type
		$member = new Member();
		$user = new User();
		$sales_type_ar = Input::get('salestype_ar');
		$is_service_ar = Input::get('is_service_ar');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$agent_id = Input::get('agent_id');
		$branch_id = Input::get('branch_id');
		if($dt1 && $dt2){

			$start_date = strtotime($dt1);
			$end_date = strtotime($dt2 . "1 day -1sec");

		} else {

			// get cur month
			$dt = date('F Y');
			$start_date = strtotime($dt);
			$end_date = strtotime($dt . "1 month -1sec");

		}
		//backhere2

		echo "<div class='text-right'><button id='btnArExcel' class='btn btn-default'><i class='fa fa-download'></i> Download</button> <button id='btnSummary' class='btn btn-default'><i class='fa fa-pie-chart'></i> Summary</button></div><br>";

		echo "<table id='tblARaccounting' class='table table-bordered' style='font-size:11px;'>";

		$arr_all = [];
		$branch_id = Input::get('branch_id');
		if(!$user->hasPermission('credit_all')){
			$branch_id = $user->data()->branch_id;
		}
		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}
		if($agent_id){
			$user_id = $agent_id;
		}
	//	foreach($types as $type) {
			// get sales for this type
			$cur_sales_type = new Sales_type($sales_type_ar);
			$ar_member = $member->salesByType($cur_sales_type->data()->id, $start_date, $end_date,0,$branch_id,$user_id);
			$total_all = 0;
			$total_amount_all = 0;
			$total_paid_all = 0;
				$totalcol = 10;
			if($ar_member) {


				echo "<tr><th colspan='$totalcol' style='font-size:30px;'>".$cur_sales_type->data()->name."</th></tr>";
				echo "<tr><th>" . MEMBER_LABEL . "</th><th>" . INVOICE_LABEL . "</th><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th><th>Date</th><th class='text-right'>Total Amount</th><th class='text-right'>Amount Paid</th><th class='text-right'>Not Matured Cheque</th><th class='text-right'>Balance</th><th>Terms</th><th class='text-right'>Days</th><th>Due</th></tr>";
				$prevName = "";
				$total_per_member = 0;
				$total_amount_per_member = 0;
				$total_paid_per_member = 0;
				$first = true;

				foreach($ar_member as $ar) {
					/*
					$pending_amount = $ar->pending_amount - $ar->amount_paid;
					$total_amount = $ar->pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
					$amount_paid = $ar->amount_paid +  $ar->valid_cheque + $ar->invalid_cheque;
					*/
					$pending_amount = $ar->pending_amount - $ar->amount_paid  + $ar->invalid_cheque;
				//	$total_amount = $ar->pending_amount  + $ar->valid_cheque + $ar->invalid_cheque;
				//	$amount_paid = $ar->amount_paid +  $ar->valid_cheque;

				$total_amount = $ar->pending_amount;
				$amount_paid = $ar->amount_paid;

					if(!$pending_amount) continue;

					$total_all += $pending_amount;
					$total_amount_all += $total_amount;
					$total_paid_all += $amount_paid;

					$clien_name = "";

					if($prevName != $ar->lastname) {
						$clien_name = $ar->lastname;
						if($first) {
							$first = false;
						} else {
							echo "<tr><th colspan='" . ($totalcol - 5) . "'>Total Balance</th><th colspan='1' class='text-right'>" . number_format($total_amount_per_member, 2) . "</th><th colspan='1' class='text-right'>" . number_format($total_paid_per_member, 2) . "</th><th></th><th colspan='1' class='text-right'>" . number_format($total_per_member, 2) . "</th><th></th><th></th><th></th></tr>";
						}
						$total_per_member = 0;
						$total_amount_per_member = 0;
						$total_paid_per_member = 0;
					}
					$prevName = $ar->lastname;

					$total_per_member += $pending_amount;
					$total_amount_per_member += $total_amount;
					$total_paid_per_member += $amount_paid;
					if(isset($arr_all[$cur_sales_type->data()->name])) {
						$arr_all[$cur_sales_type->data()->name] += $pending_amount;
					} else {
						$arr_all[$cur_sales_type->data()->name] = $pending_amount;
					}
					$member_terms = ($ar->terms) ? $ar->terms : 'N/A';
					$due_date ='';

					if($ar->terms){
						$due_date = strtotime(date('F d, Y',$ar->sold_date) . " " . $member_terms . " days" );

					}


					echo "<tr><td class='text-danger'><strong>$clien_name</strong></td><td>$ar->invoice</td><td>$ar->dr</td><td>$ar->ir</td><td>".date('F d, Y',$ar->sold_date)."</td><td class='text-right'>" . number_format($total_amount, 2) . "</td><td class='text-right'>" . number_format($amount_paid, 2) . "</td><td  class='text-right'>$ar->invalid_cheque</td><td class='text-right'>" . number_format($pending_amount, 2) . "</td><td>".$member_terms."</td><td class='text-right'>".getTime(time() - $ar->sold_date)."</td><td>".$due_date."</td></tr>";

				}
				if($total_per_member) echo "<tr><th colspan='" . ($totalcol - 5) . "'>Total Balance</th><th colspan='1' class='text-right'>" . number_format($total_amount_per_member, 2) . "</th><th colspan='1' class='text-right'>" . number_format($total_paid_per_member, 2) . "</th><th></th><th colspan='1' class='text-right '>" . number_format($total_per_member, 2) . "</th><th></th><th></th><th></th></tr>";
				$total_all = number_format($total_all,2);
				echo "<tr><th colspan='".($totalcol-5)."' class='text-right'>Total ".$cur_sales_type->data()->name."</th><th colspan='1' class='text-right text-danger'>" . number_format($total_amount_all, 2) . "</th><th colspan='1' class='text-right text-danger'>" . number_format($total_paid_all, 2) . "</th><th></th><th class='text-right'><strong class='text-danger'>$total_all</strong></th><th></th><th></th><th></th></tr>"; // space for the next entry
			} else {
				echo "<tr><th colspan='$totalcol'>No record found</th></tr>";
			}
	//	}
		echo "</table>";

		echo "<input type='hidden' id='summary_details' value='" . json_encode($arr_all, true) . "'>";
	}

	function ar2(){

		// select member credit
		// select not yet matured
		// select valid
		// join sa sales

		$arr_type = [];
		$user = new User();
		$sales_type_ar = Input::get('salestype_ar');
		$is_service_ar = Input::get('is_service_ar');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$agent_id = Input::get('agent_id');
		$branch_id = Input::get('branch_id');
		$date_type = Input::get('date_type');

		if(!$branch_id){
			if(!$user->hasPermission('credit_all')){
				$branch_id = $user->data()->branch_id;
			}
		}

		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}

		$member = new Member();
		if($sales_type_ar == -1){
		$sales_type_ar = 0;
		}

		$mc_result = $member->memberCreditUnpaid($sales_type_ar,$branch_id,$user_id,$is_service_ar,$dt1,$dt2,$agent_id,$date_type);

		// add here all uncollected cheque without member_credit
		if($mc_result){
			echo "<div class='text-right'>
					<button id='btnArExcel' class='btn btn-default'><i class='fa fa-download'></i> Download</button>
				</div>
				<br>";

			foreach($mc_result as $mc_item){
			    $mc_item->sales_type_name = ($mc_item->sales_type_name) ? $mc_item->sales_type_name  : "No type" ;
				$arr_type[$mc_item->sales_type_name][$mc_item->member_name][] = $mc_item;
			}
				echo "<p><span style='display: inline-block;width:15px;height: 15px' class='bg-danger'></span> For collection</p>";
				echo "<table class='table table-bordered table-condensed'>";
				$colspan = 12;
				$border_top = 'border:1px solid #ccc;';
			$total_amount_all = 0;
			$total_pending_all = 0;
			$total_paid_all = 0;
			$total_invalid_cheque_all = 0;
			foreach($arr_type as $sales_type => $member_sales){
				echo "<tr class='bg-success'><th colspan='$colspan'>$sales_type</th></tr>";
				foreach($member_sales as $member_name => $member_credit){
					//	echo "<tr><th colspan='$colspan'>$member_name</th></tr>";
					$due_date = 0;
					$member_terms = 0;
					$pending_amount = 0;


					echo "<tr><th>" . MEMBER_LABEL . "</th><th>" . INVOICE_LABEL . "</th><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th><th>Date</th><th class='text-right'>Total Amount</th><th class='text-right'>Amount Paid</th><th class='text-right'>Not Matured Cheque</th><th class='text-right'>Balance</th><th>Terms</th><th class='text-right'>Days</th><th>Due</th></tr>";
					$mfirst =true;

					$total_amount_per_member = 0;
					$total_pending_per_member = 0;
					$total_paid_per_member = 0;
					$total_invalid_cheque_per_member = 0;

					foreach($member_credit as $md){
						$invalid_cheque = ($md->invalid_cheque) ? $md->invalid_cheque : 0;
						$pending_amount = $md->amount - $md->amount_paid; // add invalid cheque
						$total_amount = $md->amount;
						$amount_paid = $md->amount_paid;
						$member_terms = ($md->terms) ? $md->terms : $md->member_terms;
						$member_terms = ($member_terms) ? $member_terms : 0;
						$member_name_f='';
						if($mfirst){
						$member_name_f = $member_name;
						$mfirst = false;
						}
						$total_amount_per_member += $total_amount;
						$total_paid_per_member += $amount_paid;
						$total_pending_per_member += $pending_amount;
						$total_invalid_cheque_per_member += $invalid_cheque;
						$due_date ='';
						if($member_terms){
							$due_date = strtotime(date('m/d/Y',$md->sold_date) . " " . $member_terms . " days" );
						} else {
							$due_date = strtotime(date('m/d/Y',$md->sold_date));
						}
						$bgwarning = "";
						if(time() > $due_date){
							$bgwarning ='bg-danger';
						}

						if($date_type){
							if($md->is_scheduled){
								$dt_sold = date('m/d/Y',$md->is_scheduled);
							} else {
								 $dt_sold = date('m/d/Y',$md->sold_date);
							}


						 } else {

						    $dt_sold = date('m/d/Y',$md->sold_date);

						 }

						echo "<tr class='$bgwarning'>
							<td style='$border_top' class='text-danger'><strong>$member_name_f</strong></td>
							<td style='$border_top'>$md->invoice</td>
							<td style='$border_top'>$md->dr</td>
							<td style='$border_top'>$md->ir</td>
							<td style='$border_top'>".$dt_sold ."</td>
							<td style='$border_top' class='text-right'>" . number_format($total_amount, 2) . "</td>
							<td style='$border_top' class='text-right'>" . number_format($amount_paid, 2) . "</td>
							<td style='$border_top' class='text-right'>$invalid_cheque</td>
							<td style='$border_top' class='text-right'>" . number_format($pending_amount, 2) . "</td>
							<td style='$border_top' >".$member_terms."</td>
							<td style='$border_top' class='text-right'>".getTime(time() - $md->sold_date)."</td>
							<td style='$border_top'>".date('m/d/Y',$due_date)."</td>
							</tr>";
					}
					echo "<tr>";
					echo "<td  style='$border_top' class='text-right'></td><td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_amount_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_paid_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_invalid_cheque_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_pending_per_member,2)."</td>";
					echo "<td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td>";

					echo "</tr>";
					$total_amount_all +=$total_amount_per_member ;
					$total_pending_all += $total_pending_per_member;
					$total_paid_all +=  $total_paid_per_member;
					$total_invalid_cheque_all += $total_invalid_cheque_per_member;
				}
			}
					echo "<tr class=''><th colspan='$colspan'>GRAND TOTAL</th></tr>";
					echo "<tr>";
					echo "<th  style='$border_top' class='text-right'></th><th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_amount_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_paid_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_invalid_cheque_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_pending_all,2)."</th>";
					echo "<th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th>";

					echo "</tr>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'>No record.</div>";
		}
	}

	function printUnpaidCredit(){


		$arr_type = [];
		$user = new User();
		$sales_type_ar = Input::get('salestype_ar');
		$is_service_ar = Input::get('is_service_ar');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$agent_id = Input::get('agent_id');
		$branch_id = Input::get('branch_id');
		$date_type = Input::get('date_type');

		if(!$branch_id){
			if(!$user->hasPermission('credit_all')){
				$branch_id = $user->data()->branch_id;
			}
		}

		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}

		$member = new Member();
		if($sales_type_ar == -1){
		$sales_type_ar = 0;
		}

		$mc_result = $member->memberCreditUnpaid($sales_type_ar,$branch_id,$user_id,$is_service_ar,$dt1,$dt2,$agent_id,$date_type);

		// add here all uncollected cheque without member_credit
		if($mc_result){


			foreach($mc_result as $mc_item){
			    $mc_item->sales_type_name = ($mc_item->sales_type_name) ? $mc_item->sales_type_name  : "No type" ;
				$arr_type[$mc_item->sales_type_name][$mc_item->member_name][] = $mc_item;
			}

			echo "<p><strong>Account Receivables All Salesman</strong></p>";
			echo "<p><strong>As of ".date('m/d/Y')."</strong></p>";
			echo "<table class='table table-bordered table-condensed'>";
			$colspan = 9;
			$border_top = 'border:1px solid #ccc;';
			$total_amount_all = 0;
			$total_pending_all = 0;
			$total_paid_all = 0;
			$total_invalid_cheque_all = 0;

			foreach($arr_type as $sales_type => $member_sales){
				echo "<tr><th>" . MEMBER_LABEL . "</th><th>INVOICE</th><th>DATE</th><th>BRANCH</th><th>DR AMOUNT</th><th>COLLECTION AMOUNT</th><th>DEDUCTION AMOUNT</th><th>BALANCE</th><th>Days Lapse</th></tr>";

				echo "<tr class='bg-success'><th colspan='$colspan'>$sales_type</th></tr>";
				foreach($member_sales as $member_name => $member_credit){
					//	echo "<tr><th colspan='$colspan'>$member_name</th></tr>";
					$due_date = 0;
					$member_terms = 0;
					$pending_amount = 0;


					$mfirst =true;

					$total_amount_per_member = 0;
					$total_pending_per_member = 0;
					$total_paid_per_member = 0;
					$total_invalid_cheque_per_member = 0;

					foreach($member_credit as $md){
						$invalid_cheque = ($md->invalid_cheque) ? $md->invalid_cheque : 0;
						$pending_amount = $md->amount - $md->amount_paid; // add invalid cheque
						$total_amount = $md->amount;
						$amount_paid = $md->amount_paid;
						$member_terms = ($md->terms) ? $md->terms : $md->member_terms;
						$member_terms = ($member_terms) ? $member_terms : 0;
						$member_name_f = '';
						$member_terms_g = '';
						$credit_limit = '';
						$member_region = $md->region;
						$deposits = 0;
						if($mfirst){
							$member_name_f = $member_name;
							$member_terms_g = $md->terms;
							$credit_limit = $md->credit_limit;
							$mfirst = false;
							echo "<tr >
							<td style='$border_top'><strong>$member_name_f</strong></td>
							<td style='$border_top'><strong>Credit Limit</strong></td>
							<td style='$border_top'><strong>$credit_limit</strong></td>
							<td style='$border_top'><strong>Terms</strong></td>
							<td style='$border_top' class='text-right'><strong>$member_terms_g</strong></td>
							<td></td><td></td><td></td><td></td>
							</tr>";

						}
						$total_amount_per_member += $total_amount;
						$total_paid_per_member += $amount_paid;
						$total_pending_per_member += $pending_amount;
						$total_invalid_cheque_per_member += $invalid_cheque;
						$due_date ='';
						if($member_terms){
							$due_date = strtotime(date('m/d/Y',$md->sold_date) . " " . $member_terms . " days" );
						} else {
							$due_date = strtotime(date('m/d/Y',$md->sold_date));
						}
						$bgwarning = "";
						if(time() > $due_date){
							$bgwarning ='bg-danger';
						}

						if($date_type){
							if($md->is_scheduled){
								$dt_sold = date('m/d/Y',$md->is_scheduled);
							} else {
								 $dt_sold = date('m/d/Y',$md->sold_date);
							}
						 } else {
						    $dt_sold = date('m/d/Y',$md->sold_date);
						 }
						 $ctrl = "";
						 if($md->invoice){
							 $ctrl = $md->invoice;
						 } else if ($md->dr){
							 $ctrl = $md->dr;
						 } else if ($md->ir){
							 $ctrl = $md->ir;
						 } else if ($md->sr){
							 $ctrl = $md->sr;
						 } else if ($md->ts){
							 $ctrl = $md->ts;
						 }

						echo "<tr class='$bgwarning'>
								<td style='$border_top'></td>
								<td style='$border_top'>$ctrl</td>
								<td style='$border_top'>$dt_sold</td>
								<td style='$border_top' >$member_region</td>
								<td style='$border_top' class='text-right'>" . number_format($total_amount, 2) . "</td>
								<td style='$border_top' class='text-right'>" . number_format($amount_paid, 2) . "</td>
								<td style='$border_top' class='text-right'>".number_format($deposits,2)."</td>
								<td style='$border_top' class='text-right'>" . number_format($pending_amount, 2) . "</td>
								<td style='$border_top'></td>
							</tr>";

					}

					echo "<tr>";
					echo "<td  style='$border_top'></td>";
					echo "<td  style='$border_top'></td>";
					echo "<td  style='$border_top'></td>";
					echo "<td  style='$border_top'>Sub Total</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_amount_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_paid_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format(0,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_pending_per_member,2)."</td>";

					echo "<td  style='$border_top'></td>";
					echo "</tr>";
					$total_amount_all +=$total_amount_per_member ;
					$total_pending_all += $total_pending_per_member;
					$total_paid_all +=  $total_paid_per_member;
					$total_invalid_cheque_all += $total_invalid_cheque_per_member;
				}
			}
					echo "<tr class=''><th colspan='$colspan'>GRAND TOTAL</th></tr>";
					echo "<tr>";
					echo "<th  style='$border_top'></th>";
					echo "<th  style='$border_top'></th>";
					echo "<th  style='$border_top'></th>";
					echo "<th  style='$border_top'></th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_amount_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_paid_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_invalid_cheque_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_pending_all,2)."</th>";
					echo "<th  style='$border_top'></th>";


					echo "</tr>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'>No record.</div>";
		}
	}

	function graphSalesMonthly(){

		$dt = Input::get('dt');
		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->getSalesTypeSummary($user->data()->company_id,$dt);
		$sales_type = new Sales_type();
		$sales_types = $sales_type->get_active('salestypes',['company_id','=',$user->data()->company_id]);

		$results = [];
		if($data){
			foreach($data as $d){
				$results[$d->sales_type][$d->m] = $d->totalamount;
			}
		}


		$monthly = [];

		foreach($sales_types as $st){
			$st_id  = $st->id;

			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$st_id][$i]) ? $results[$st_id][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;


			}

		}

		$total_monthly= 0;
		$arr = [];
		for($i=1;$i<=12;$i++){
			$total_monthly  += $monthly[$i];
			$monthNum  = $i;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('M');
				$obj['y'] =  $monthName;
				$obj['a'] = $monthly[$i];
				array_push($arr,$obj);
		}
		if($arr){
			echo json_encode($arr);
		} else {
			echo json_encode(array('error' => true));
		}
	}
	function excelAr() {

		$filename = "accounts-receivable-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		// select distinct sales type
		$member = new Member();
		$user = new User();
		$sales_type_ar = Input::get('sales_type_ar');
		$sales_type = new Sales_type($sales_type_ar);


		// get cur month
		$dt = date('F Y');
		$start_date = strtotime($dt);
		$end_date = strtotime($dt . "1 month -1sec");
		echo "<table border='1' id='tblARaccounting' class='table table-bordered'>";
		$arr_all = [];
		$branch_id = Input::get('branch_id');
		if(!$user->hasPermission('credit_all')){
			$branch_id = $user->data()->branch_id;
		}
		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}

			// get sales for this type
			$ar_member = $member->salesByType($sales_type->data()->id, $start_date, $end_date,0,$branch_id,$user_id);
			$total_all = 0;
			$total_amount_all = 0;
			$total_paid_all = 0;
			if($ar_member) {
				$totalcol = 10;
				echo "<tr><th colspan='$totalcol' style='font-size:30px;'>".$sales_type->data()->name."</th></tr>";
				echo "<tr><th>" . MEMBER_LABEL . "</th><th>" . INVOICE_LABEL . "</th><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th><th>Date</th><th class='text-right'>Total Amount</th><th class='text-right'>Amount Paid</th><th>Not valid cheque</th><th class='text-right'>Balance</th><th>Terms</th><th class='text-right'>Days</th></tr>";
				$prevName = "";
				$total_per_member = 0;
				$total_amount_per_member = 0;
				$total_paid_per_member = 0;
				$first = true;

				foreach($ar_member as $ar) {
					/*
					$pending_amount = $ar->pending_amount - $ar->amount_paid;
					$total_amount = $ar->pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
					$amount_paid = $ar->amount_paid +  $ar->valid_cheque + $ar->invalid_cheque;
*/

					 $pending_amount = $ar->pending_amount - $ar->amount_paid  + $ar->invalid_cheque;
					$total_amount = $ar->pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
					$amount_paid = $ar->amount_paid +  $ar->valid_cheque;


					if(!$pending_amount) continue;
					$total_all += $pending_amount;
					$total_amount_all += $total_amount;
					$total_paid_all += $amount_paid;
					$clien_name = "";
					if($prevName != $ar->lastname) {
						$clien_name = $ar->lastname;
						if($first) {
							$first = false;
						} else {
							echo "<tr><th colspan='" . ($totalcol - 5) . "'>Total Balance</th><th class='text-right'>" . number_format($total_amount_per_member, 2) . "</th><th class='text-right'>" . number_format($total_paid_per_member, 2) . "</th><th></th><th colspan='1' class='text-right'>" . number_format($total_per_member, 2) . "</th><th></th><th></th></tr>";
						}
						$total_per_member = 0;
						$total_amount_per_member = 0;
						$total_paid_per_member = 0;
					}
					$prevName = $ar->lastname;

					$total_per_member += $pending_amount;
					$total_amount_per_member += $total_amount;
					$total_paid_per_member += $amount_paid;
					if(isset($arr_all[$sales_type->data()->name])) {
						$arr_all[$sales_type->data()->name] += $pending_amount;
					} else {
						$arr_all[$sales_type->data()->name] = $pending_amount;
					}
					$member_terms = ($ar->terms) ? $ar->terms : 'N/A';
					echo "<tr><td class='text-danger'><strong>$clien_name</strong></td><td>$ar->invoice</td><td>$ar->dr</td><td>$ar->ir</td><td>".date('F d, Y',$ar->sold_date)."</td><td class='text-right'>" . number_format($total_amount, 2) . "</td><td class='text-right'>" . number_format($amount_paid, 2) . "</td><td>$ar->invalid_cheque</td><td class='text-right'>" . number_format($pending_amount, 2) . "</td><td>".$member_terms."</td><td class='text-right'>".getTime(time() - $ar->sold_date)."</td></tr>";

				}
				if($total_per_member) echo "<tr><th colspan='" . ($totalcol - 5) . "'>Total Balance</th><th class='text-right'>" . number_format($total_amount_per_member, 2) . "</th><th class='text-right'>" . number_format($total_paid_per_member, 2) . "</th><th></th><th colspan='1' class='text-right'>" . number_format($total_per_member, 2) . "</th><th></th><th></th></tr>";
				$total_all = number_format($total_all,2);
				echo "<tr><th colspan='" . ($totalcol - 5) . "' class='text-right'>Total $sales_type->data()->name</th><th class='text-right text-danger'>" . number_format($total_amount_all, 2) . "</th><th class='text-right text-danger'>" . number_format($total_paid_all, 2) . "</th><th></th><th><strong class='text-danger'>$total_all</strong></th><th></th><th></th></tr>"; // space for the next entry
			}

		echo "</table>";
	/*	echo "<table border=1>";
		echo "<tr><th colspan='2'>SUMMARY</th></tr>";
		$totalsummary = 0;
		foreach($arr_all as $key => $val){
			$totalsummary += $val;
			echo "<tr><td>$key</td><td>".number_format($val,2)."</td></tr>";
		}
		echo "<tr><th>Total</th><th>".number_format($totalsummary,2)."</th></tr>";
		echo "</table>"; */
	//	echo "<input type='hidden' id='summary_details' value='" . json_encode($arr_all, true) . "'>";

	}

	function excelAr2() {

		$filename = "accounts-receivable-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		// select member credit
		// select not yet matured
		// select valid
		// join sa sales

		$arr_type = [];
		$user = new User();
		$sales_type_ar = Input::get('salestype_ar');
		$is_service_ar = Input::get('is_service_ar');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$agent_id = Input::get('agent_id');
		$branch_id = Input::get('branch_id');
		$date_type = Input::get('date_type');

		if(!$branch_id){
			if(!$user->hasPermission('credit_all')){
				$branch_id = $user->data()->branch_id;
			}
		}

		$user_id = 0;
		if($user->hasPermission('wh_agent')){
			$user_id = $user->data()->id;
		}

		$member = new Member();
		if($sales_type_ar == -1){
			$sales_type_ar = 0;
		}

		$mc_result = $member->memberCreditUnpaid($sales_type_ar,$branch_id,$user_id,$is_service_ar,$dt1,$dt2,$agent_id,$date_type);

		// add here all uncollected cheque without member_credit
		if($mc_result){


			foreach($mc_result as $mc_item){
			    $mc_item->sales_type_name = ($mc_item->sales_type_name) ? $mc_item->sales_type_name  : "No type" ;
				$arr_type[$mc_item->sales_type_name][$mc_item->member_name][] = $mc_item;
			}
			echo "<table class='table table-bordered table-condensed'>";
			$colspan = 12;
			$border_top = 'border:1px solid #ccc;';
			$total_amount_all = 0;
			$total_pending_all = 0;
			$total_paid_all = 0;
			$total_invalid_cheque_all = 0;
			foreach($arr_type as $sales_type => $member_sales){
				echo "<tr class='bg-success'><th colspan='$colspan'>$sales_type</th></tr>";
				foreach($member_sales as $member_name => $member_credit){
					//	echo "<tr><th colspan='$colspan'>$member_name</th></tr>";
					$due_date = 0;
					$member_terms = 0;
					$pending_amount = 0;


					echo "<tr><th>" . MEMBER_LABEL . "</th><th>" . INVOICE_LABEL . "</th><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th><th>Date</th><th class='text-right'>Total Amount</th><th class='text-right'>Amount Paid</th><th class='text-right'>Not Matured Cheque</th><th class='text-right'>Balance</th><th>Terms</th><th class='text-right'>Days</th><th>Due</th></tr>";
					$mfirst =true;

					$total_amount_per_member = 0;
					$total_pending_per_member = 0;
					$total_paid_per_member = 0;
					$total_invalid_cheque_per_member = 0;

					foreach($member_credit as $md){
						$invalid_cheque = ($md->invalid_cheque) ? $md->invalid_cheque : 0;
						$pending_amount = $md->amount - $md->amount_paid; // add invalid cheque
						$total_amount = $md->amount;
						$amount_paid = $md->amount_paid;
						$member_terms = ($md->terms) ? $md->terms : $md->member_terms;
						$member_terms = ($member_terms) ? $member_terms : 0;
						$member_name_f='';
						if($mfirst){
						$member_name_f = $member_name;
						$mfirst = false;
						}
						$total_amount_per_member += $total_amount;
						$total_paid_per_member += $amount_paid;
						$total_pending_per_member += $pending_amount;
						$total_invalid_cheque_per_member += $invalid_cheque;
						$due_date ='';
						if($member_terms){
							$due_date = strtotime(date('m/d/Y',$md->sold_date) . " " . $member_terms . " days" );
						} else {
							$due_date = strtotime(date('m/d/Y',$md->sold_date));
						}
						$bgwarning = "";
						if(time() > $due_date){
							$bgwarning ='bg-danger';
						}

						if($date_type){
							if($md->is_scheduled){
								$dt_sold = date('m/d/Y',$md->is_scheduled);
							} else {
								 $dt_sold = date('m/d/Y',$md->sold_date);
							}


						 } else {

						    $dt_sold = date('m/d/Y',$md->sold_date);

						 }

						echo "<tr class='$bgwarning'>
							<td style='$border_top' class='text-danger'><strong>$member_name_f</strong></td>
							<td style='$border_top'>$md->invoice</td>
							<td style='$border_top'>$md->dr</td>
							<td style='$border_top'>$md->ir</td>
							<td style='$border_top'>".$dt_sold ."</td>
							<td style='$border_top' class='text-right'>" . number_format($total_amount, 2) . "</td>
							<td style='$border_top' class='text-right'>" . number_format($amount_paid, 2) . "</td>
							<td style='$border_top' class='text-right'>$invalid_cheque</td>
							<td style='$border_top' class='text-right'>" . number_format($pending_amount, 2) . "</td>
							<td style='$border_top' >".$member_terms."</td>
							<td style='$border_top' class='text-right'>".getTime(time() - $md->sold_date)."</td>
							<td style='$border_top'>".date('m/d/Y',$due_date)."</td>
							</tr>";
					}
					echo "<tr>";
					echo "<td  style='$border_top' class='text-right'></td><td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_amount_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_paid_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_invalid_cheque_per_member,2)."</td>";
					echo "<td  style='$border_top' class='text-right'>" . number_format($total_pending_per_member,2)."</td>";
					echo "<td  style='$border_top'></td><td  style='$border_top'></td><td  style='$border_top'></td>";

					echo "</tr>";
					$total_amount_all +=$total_amount_per_member ;
					$total_pending_all += $total_pending_per_member;
					$total_paid_all +=  $total_paid_per_member;
					$total_invalid_cheque_all += $total_invalid_cheque_per_member;
				}
			}
					echo "<tr class=''><th colspan='$colspan'>GRAND TOTAL</th></tr>";
					echo "<tr>";
					echo "<th  style='$border_top' class='text-right'></th><th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_amount_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_paid_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_invalid_cheque_all,2)."</th>";
					echo "<th  style='$border_top' class='text-right'>" . number_format($total_pending_all,2)."</th>";
					echo "<th  style='$border_top'></th><th  style='$border_top'></th><th  style='$border_top'></th>";

					echo "</tr>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'>No record.</div>";
		}
	}

	function getSOA() {
		$member_id = Input::get('member_id');
		$soa_cancel = Input::get('soa_cancel');
		$soa_fully_paid = Input::get('soa_fully_paid');

		if(is_numeric($member_id)) {
			$sales_type = new Sales_type();
			$user = new User();
			$member = new Member();

			// get cur month
			$dt = date('F Y');
			$start_date = strtotime($dt);
			$end_date = strtotime($dt . "1 month -1sec");
			echo "<div class='text-right'><button id='btnExcelSOA'  data-member_id='$member_id' class='btn btn-default'><i class='fa fa-download'></i> Download</button> <button id='btnPrintSOA'  data-member_id='$member_id' class='btn btn-default'><i class='fa fa-print'></i> Print</button></div><br>";
			echo "<table id='tblARaccounting' class='table table-bordered'>";
				$ar_member = $member->salesByType(0, $start_date, $end_date,$member_id,0,0,$soa_cancel,$soa_fully_paid,1);
				if($ar_member) {
					$totalcol = 9;
					echo "<tr><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th><th>Dr Date</th><th  class='text-right'>Amount Due</th><th>" . INVOICE_LABEL . "</th><th>PO No.</th><th class='text-right'>Amount Paid</th><th class='text-right'>Balance</th><th class='text-right'>Age</th></tr>";
					$prevName = "";

					$first = true;
					$total_per_member = 0;
					$total_sold = 0;
					$total_paid = 0;
					$total_freight = 0;

					foreach($ar_member as $ar) {

						$pending_amount = $ar->pending_amount - $ar->amount_paid;
						//$total_amount = $pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
						//$amount_paid = $ar->amount_paid +  $ar->valid_cheque + $ar->invalid_cheque;
						$total_amount = $ar->pending_amount;
						$amount_paid = $ar->amount_paid;
						//if(!$pending_amount) continue;



						$member_terms = ($ar->terms) ? $ar->terms : 'N/A';
						if($ar->is_scheduled){
							$date_sched = date('F d, Y',$ar->is_scheduled);
						} else {
							$date_sched = ($ar->sold_date) ? date('F d, Y',$ar->sold_date) : 'N/A';
						}
						$freight = $ar->freight_charge ? $ar->freight_charge : 0;

						$freight_lbl = "";
						if($freight){
							$freight_lbl .= "<small class='span-block text-danger'>Freight: " . number_format($freight,2). "</small>";
							$total_freight += $freight;
							$pending_amount += $freight;
						}
						$bg = "";
						$fp = "";
						if($ar->status == 1){
							$bg = "bg-danger";
						}

						if(!$pending_amount){
							$fp = "<small class='span-block text-danger'>Fully Paid</small>";
						}
						if(!$ar->pending_amount){
							$pending_amount = 0;
							$amount_paid = $ar->saletotal;
							$total_amount =  $ar->saletotal;
						}

						echo "<tr class='$bg'><td>".DR_PREFIX."$ar->dr</td><td>$ar->ir</td><td>$date_sched</td><td class='text-right'>" . number_format($total_amount, 2) . " $freight_lbl</td><td>$ar->invoice</td><td>$ar->client_po</td><td class='text-right'>" . number_format($amount_paid, 2) . "</td><td class='text-right'>" . number_format($pending_amount, 2) . " $fp</td><td class='text-right'>".getTime(time() - strtotime($date_sched))."</td></tr>";
						$total_per_member += $pending_amount;
						$total_sold += $total_amount;
						$total_paid += $amount_paid;
					}
					// Select cash , bank transfer, credit

					 echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Sold</th><th colspan='1' class='text-right'>" . number_format($total_sold, 2) . "</th><th></th></tr>";
					 echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Freight</th><th colspan='1' class='text-right'>" . number_format($total_freight, 2) . "</th><th></th></tr>";
					 echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Paid</th><th colspan='1' class='text-right'>" . number_format($total_paid, 2) . "</th><th></th></tr>";
					echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Balance</th><th colspan='1' class='text-right'>" . number_format($total_per_member, 2) . "</th><th></th></tr>";
					echo "<tr><th colspan='$totalcol'>&nbsp;</th></tr>"; // space for the next entry
				}
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'><i class='fa fa-search'></i> Search Client</div>";
		}
	}

	function excelSOA() {
		$filename = "statement-of-account-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$member_id = Input::get('member_id');
		$soa_cancel = Input::get('soa_cancel');
		$soa_fully_paid = Input::get('soa_fully_paid');

		if(is_numeric($member_id)) {
			$sales_type = new Sales_type();
			$user = new User();
			$member = new Member();

			// get cur month
			$dt = date('F Y');
			$start_date = strtotime($dt);
			$end_date = strtotime($dt . "1 month -1sec");
			echo "<table border='1' id='tblARaccounting' class='table table-bordered'>";
			$ar_member = $member->salesByType(0, $start_date, $end_date,$member_id,0,0,$soa_cancel,$soa_fully_paid,1);
			if($ar_member) {
				$totalcol = 10;

				echo "<tr><th>" . DR_LABEL . "</th><th>" . PR_LABEL . "</th>";

				echo "<th>Dr Date</th>";
				echo "<th  class='text-right'>Amount Due</th>";
				echo "<th>" . INVOICE_LABEL . "</th>";
				echo "<th>PO No.</th>";
				echo "<th class='text-right'>Amount Paid</th>";
				echo "<th class='text-right'>Freight</th>";
				echo "<th class='text-right'>Balance</th>";
				echo "<th class='text-right'></th>";
				echo "</tr>";

				$prevName = "";

				$first = true;
				$total_per_member = 0;
				$total_sold = 0;
				$total_paid = 0;
				$total_freight = 0;

				foreach($ar_member as $ar) {

					$pending_amount = $ar->pending_amount - $ar->amount_paid;
						//$total_amount = $pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
						//$amount_paid = $ar->amount_paid +  $ar->valid_cheque + $ar->invalid_cheque;
						$total_amount = $ar->pending_amount;
						$amount_paid = $ar->amount_paid;
						//if(!$pending_amount) continue;



						$member_terms = ($ar->terms) ? $ar->terms : 'N/A';
						if($ar->is_scheduled){
							$date_sched = date('F d, Y',$ar->is_scheduled);
						} else {
							$date_sched = ($ar->sold_date) ? date('F d, Y',$ar->sold_date) : 'N/A';
						}
						$freight = $ar->freight_charge ? $ar->freight_charge : 0;

						$freight_lbl = "";
						if($freight){
							$freight_lbl = number_format($freight,2);
							$total_freight += $freight;
							$pending_amount += $freight;
						}
						$bg = "";
						$fp = "";
						if($ar->status == 1){
							$bg = "bg-danger";
						}

						if(!$pending_amount){
							$fp = "<small class='span-block text-danger'>Fully Paid</small>";
						}
						if(!$ar->pending_amount){
							$pending_amount = 0;
							$amount_paid = $ar->saletotal;
							$total_amount =  $ar->saletotal;
						}

					echo "<tr>";
					echo "<td>$ar->dr</td><td>$ar->ir</td>";

					echo "<td>$date_sched</td>";
					echo "<td class='text-right'>" . number_format($total_amount, 2) . "</td>";
					echo "<td>$ar->invoice</td>";
					echo "<td>$ar->client_po</td>";
					echo "<td class='text-right'>" . number_format($amount_paid, 2) . "</td>";
					echo "<td class='text-right'>" . $freight_lbl . "</td>";
					echo "<td class='text-right'>" . number_format($pending_amount, 2) . "</td>";
					echo "<td class='text-right'>".getTime(time() - strtotime($date_sched))."</td>";
					echo "</tr>";
					$total_per_member += $pending_amount;
					$total_sold += $total_amount;
					$total_paid += $amount_paid;

				}

				echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Sold</th><th colspan='1' class='text-right'>" . number_format($total_sold, 2) . "</th><th></th></tr>";
				echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Freight</th><th colspan='1' class='text-right'>" . number_format($total_freight, 2) . "</th><th></th></tr>";
				echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Paid</th><th colspan='1' class='text-right'>" . number_format($total_paid, 2) . "</th><th></th></tr>";
				echo "<tr><th colspan='" . ($totalcol - 2) . "'>Total Balance</th><th colspan='1' class='text-right'>" . number_format($total_per_member, 2) . "</th><th></th></tr>";
				echo "<tr><th colspan='$totalcol'>&nbsp;</th></tr>"; // space for the next entry
			}
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'><i class='fa fa-search'></i> Search Client</div>";
		}

	}

	function removeCr(){
		$cr_number = Input::get('cr_number');
		if($cr_number){
			// delete cr log ids
			$user = new User();
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"Remove CR number $cr_number",
				'ajax_accounting.php'
			);

			$cr_log_ids = new Cr_log_ids();
			$cr_log_ids->deleteCr($cr_number);

			//delete cr log
			$cr_log = new Cr_log();
			$cr_log->deleteCr($cr_number);

			// delete collection report
			$collection_report = new Collection_report();
			$collection_report->deleteCr($cr_number);

			// update payments

			$payment = new Payment();
			$cr_list = $payment->getByCr($cr_number);

			if($cr_list){
				foreach($cr_list as $p){
					$cur_cr = $p->cr_number;
					if(strpos($cur_cr,',') > 0){
						// explode
						$ex = explode(",",$cur_cr);
						$rem = "";
						foreach($ex as $e){
							if(trim($e) != trim($cr_number)){
								$rem .= $e . ",";
							}
						}
						$rem = rtrim($rem,",");
						$dt = $p->cr_date;
						if(!$rem){
							$dt = 0;
						}

						if($p->id)
							$payment->update(['cr_number'=>$rem,'cr_date' => $dt], $p->id);

					} else {

						if($p->id)
							$payment->update(['cr_number'=>'','cr_date' => 0], $p->id);

					}

				}
			}
			echo "Removed successfully.";
		}
	}

	function collectionReportList(){

		$is_dl = Input::get('is_dl');

		$border = "";

		if($is_dl == 1){
			$border = "border='1'";
			$filename = "collection-report-summary" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}

		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$num_from = Input::get('num_from');
		$num_to = Input::get('num_to');
		$agent_id = Input::get('agent_id');
		$sort_type = Input::get('sort_type');
		$branch_id = Input::get('branch_id');

		$user = new User();
		$is_agent = $user->hasPermission('wh_agent');
		if($dt_from && $dt_to){

		} else {
			if($num_to && $num_from){
				$dt_from = 0;
				$dt_to = 0;
			} else {
				$dt_from = date('m/01/Y');
				$dt_to = date('m/d/Y',strtotime($dt_from . "1 month -1 min"));
			}

		}
		if(!(is_numeric($num_from) && is_numeric($num_to) && $num_from <= $num_from)){
			$num_from = 0;
			$num_to = 0;
		}

		$payment = new Payment();

		$payments = $payment->getAllCr($dt_from,$dt_to,$num_from,$num_to,$agent_id,$sort_type,$branch_id);


		?>

		<p>

			<strong>Collection Summary per CR # <br>
				For the period covered  <?php echo $dt_from; ?>  to  <?php echo $dt_to; ?>
			</strong>
		</p>
		<?php
		if($payments){
			?>


			<div id="no-more-tables">
			<div class="table-responsive">

			<table class='table' id='tblSales' <?php echo $border; ?>>
			<thead>
			<tr>
				<TH>CR Number</TH>
				<th>Date Created</th>
				<th>Receipt amount</th>
				<th>Deduction</th>
				<?php
					if(Configuration::getValue('deposits_collection') == 1){
					?>
					<th>Over Payment</th>
					<th>Other Income</th>
					<th>Freight</th>
					<?php
					}
				?>
				<th>Total</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$arr_display = [];

				foreach($payments as $p){
					$p->cr_number = trim($p->cr_number);
					$paid_amount = ($p->paid_amount) ? $p->paid_amount :0;
					$deduction = ($p->deduction) ? $p->deduction :0;
					$receipt_amount = ($p->receipt_amount) ? $p->receipt_amount :0;

					?>

					<?php

						$explode = [];

						if(strpos($p->cr_number,",") > 0){

							$explode = explode(",",$p->cr_number);

							foreach($explode as $ex){

								$collection_report = new Collection_report();
								$crdata = $collection_report->getData($ex);

								if(in_array($ex,$arr_display)){
									continue;
								}
								$arr_display[] = $ex;

								$dt_from = 0;
								$dt_to = 0;
								$paid_by = 0;
								$cashier_id = 0;
								$paid_method ="";
								$paid_by_name = "";
								$cashier_name="";
								$include_dr= "";
								$include_ir= "";
								$from_service= 0;

								if(isset($crdata->created) && $crdata->created){
									$dt_from = date('m/d/Y',$crdata->created);
								}
								if(isset($crdata->to_date) && $crdata->to_date){
									$dt_to = date('m/d/Y',$crdata->to_date);
								}

								if(isset($crdata->receive_by) && $crdata->receive_by){
								$paid_by = $crdata->receive_by;
								$paiduser = new User($crdata->receive_by);
								if(isset($paiduser->data()->firstname) && isset($paiduser->data()->lastname)){
									$paid_by_name = $paiduser->data()->firstname . " " . $paiduser->data()->lastname;
								}
							}
							if(isset($crdata->cashier_id) && $crdata->cashier_id){
								$cashier_id = $crdata->cashier_id;
								$cashieruser = new User($crdata->cashier_id);
								if(isset($cashieruser->data()->firstname) && isset($cashieruser->data()->lastname)){
									$cashier_name = $cashieruser->data()->firstname . " " . $cashieruser->data()->lastname;
								}
							}
								if(isset($crdata->remarks) && $crdata->remarks){
									$paid_method = $crdata->remarks;
								}

								if(isset($crdata->include_ov) && $crdata->include_ov){
									$include_dr = $crdata->include_ov;
								}
								if(isset($crdata->include_dv) && $crdata->include_dv){
									$include_ir = $crdata->include_dv;
								}
								if(isset($crdata->is_service) && $crdata->is_service){
									$from_service = $crdata->is_service;
								}

								if($dt_from && !$dt_to){
									$dt_to = $dt_from;
								}

								$cr_payment = new Payment();
								$current_log = $cr_payment->getCRSum($ex);

								if(isset($current_log->receipt_amount)){
									$receipt_amount = $current_log->receipt_amount;
									$deduction = $current_log->deduction;
									$paid_amount = $current_log->paid_amount;
									if($current_log->override_cr_date){
										$p->cr_date =  $current_log->override_cr_date;
									}
								}



								?>
									<tr>
										<td style='border-top:1px solid #ccc;' data-title='CR Number'><?php echo escape($ex); ?></td>
										<td style='border-top:1px solid #ccc;'  data-title='Date Created' class='text-success'>
										<?php echo date('F d, Y',$p->cr_date); ?>
										<?php if(!$is_agent){ ?>
										<button
												class='btn btn-default btnCrDate'
												data-cr_number='<?php echo $ex; ?>'
												data-cr_date='<?php echo date('m/d/Y',$p->cr_date); ?>'
												data-override='1'
												>
											<i class='fa fa-pencil'></i>
										</button>
										<?php } ?>
										</td>
										<td style='border-top:1px solid #ccc;'  data-title='Total' class=''><?php echo number_format($receipt_amount,2); ?></td>
										<td style='border-top:1px solid #ccc;'  data-title='Total' class=''><?php echo number_format($deduction,2); ?></td>

										<?php
											if(Configuration::getValue('deposits_collection') == 1){
												$total_deposits = depositByCr($ex);
												$savedOtherIncome = getSavedOtherIncome($ex);
												$other_income = $savedOtherIncome['total'] ? $savedOtherIncome['total'] : 0;
												$paid_amount = $paid_amount + $total_deposits + $other_income;
											?>
											<td  style='border-top:1px solid #ccc;'><?php echo $total_deposits; ?></td>
											<td  style='border-top:1px solid #ccc;'><?php echo $other_income; ?></td>
											<td  style='border-top:1px solid #ccc;'><?php echo $current_log->freight ; ?></td>
											<?php
											}
										?>
										<td style='border-top:1px solid #ccc;'  data-title='Total' class=''><?php echo number_format($paid_amount,2); ?></td>
										<td  style='border-top:1px solid #ccc;'>
											<?php if($is_dl != 1){
											?>
											<button class='btn btn-default btnShowDataCR'
											data-dt='<?php echo $dt_from; ?>'
											data-dt_to='<?php echo $dt_to; ?>'
											data-cashier_id='<?php echo $cashier_id; ?>'
											data-cashier_name='<?php echo $cashier_name; ?>'
											data-paid_by_name='<?php echo $paid_by_name; ?>'
											data-paid_by='<?php echo $paid_by; ?>'
											data-pm='<?php echo $paid_method; ?>'
											data-cr_number='<?php echo escape($ex); ?>'
											data-include_dr='<?php echo escape($include_dr); ?>'
											data-include_ir='<?php echo escape($include_ir); ?>'
											data-from_service='<?php echo escape($from_service); ?>'
											data-cr_date='<?php echo date('m/d/Y',$p->cr_date); ?>'

											>
											Show Data
											</button>
											<?php if(!$is_agent){
											?>
											<button class='btn btn-danger btnRemoveCR' data-cr_number='<?php echo escape($ex); ?>'>Remove</button>

											<?php
											}?>

											<?php } ?>

										</td>
									</tr>
								<?php

							}
							?>

							<?php
						} else {

							if(in_array($p->cr_number,$arr_display)){
								continue;
							}
							$arr_display[] = $p->cr_number;
							$collection_report = new Collection_report();
							$crdata = $collection_report->getData($p->cr_number);
							$dt_from = 0;
							$dt_to = 0;
							$paid_by = 0;
							$cashier_id = 0;
							$paid_method ="";
							$cashier_name = "";
							$paid_by_name = "";

							$include_dr= "";
							$include_ir= "";
							$from_service= 0;

							if(isset($crdata->created) && $crdata->created){
								$dt_from = date('m/d/Y',$crdata->created);
							}
							if(isset($crdata->to_date) && $crdata->to_date){
								$dt_to = date('m/d/Y',$crdata->to_date);
							}
							if(isset($crdata->receive_by) && $crdata->receive_by){
								$paid_by = $crdata->receive_by;
								$paiduser = new User($crdata->receive_by);
								if(isset($paiduser->data()->firstname) && isset($paiduser->data()->lastname)){
									$paid_by_name = $paiduser->data()->firstname . " " . $paiduser->data()->lastname;
								}
							}
							if(isset($crdata->cashier_id) && $crdata->cashier_id){
								$cashier_id = $crdata->cashier_id;
								$cashieruser = new User($crdata->cashier_id);
								if(isset($cashieruser->data()->firstname) && isset($cashieruser->data()->lastname)){
									$cashier_name = $cashieruser->data()->firstname . " " . $cashieruser->data()->lastname;
								}
							}
							if(isset($crdata->remarks) && $crdata->remarks){
								$paid_method = $crdata->remarks;
							}
							if(isset($crdata->include_ov) && $crdata->include_ov){
								$include_dr = $crdata->include_ov;
							}
							if(isset($crdata->include_dv) && $crdata->include_dv){
								$include_ir = $crdata->include_dv;
							}
							if(isset($crdata->is_service) && $crdata->is_service){
									$from_service = $crdata->is_service;
							}

							if($dt_from && !$dt_to){
								$dt_to = $dt_from;
							}

						?>

							<tr>
							<td style='border-top:1px solid #ccc;' data-title='CR Number'><?php echo escape($p->cr_number); ?></td>
							<td style='border-top:1px solid #ccc;'  data-title='Date Created' class='text-success'>
							<?php echo date('F d, Y',$p->cr_date); ?>
								<?php if(!$is_agent){ ?>
							<button
									class='btn btn-default btnCrDate'
									data-cr_number='<?php echo $p->cr_number; ?>'
									data-cr_date='<?php echo date('m/d/Y',$p->cr_date); ?>'
									>
								<i class='fa fa-pencil'></i>
							</button>
							<?php } ?>
							</td>
								<td style='border-top:1px solid #ccc;'  data-title='Receipt' class=''><?php echo number_format($receipt_amount,2); ?></td>
										<td style='border-top:1px solid #ccc;'  data-title='Deduction' class=''><?php echo number_format($deduction,2); ?></td>
							<?php
											if(Configuration::getValue('deposits_collection') == 1){
												$total_deposits = depositByCr($p->cr_number);
												$savedOtherIncome = getSavedOtherIncome($p->cr_number);
												$other_income = $savedOtherIncome['total'] ? $savedOtherIncome['total'] : 0;
												$paid_amount = $paid_amount + $total_deposits + $other_income;
											?>
											<td  style='border-top:1px solid #ccc;'><?php echo $total_deposits; ?></td>
											<td  style='border-top:1px solid #ccc;'><?php echo $other_income; ?></td>
											<td  style='border-top:1px solid #ccc;'><?php echo $p->freight; ?></td>
											<?php
											}
										?>
							<td style='border-top:1px solid #ccc;'  data-title='Total' class=''><?php echo number_format($paid_amount,2); ?></td>


						<td  style='border-top:1px solid #ccc;'>
								<?php if($is_dl != 1){
											?>
							<button class='btn btn-default btnShowDataCR'
								data-dt='<?php echo $dt_from; ?>'
								data-dt_to='<?php echo $dt_to; ?>'
								data-cashier_id='<?php echo $cashier_id; ?>'
								data-cashier_name='<?php echo $cashier_name; ?>'
								data-paid_by_name='<?php echo $paid_by_name; ?>'
								data-paid_by='<?php echo $paid_by; ?>'
								data-pm='<?php echo $paid_method; ?>'
								data-include_dr='<?php echo escape($include_dr); ?>'
								data-include_ir='<?php echo escape($include_ir); ?>'
								data-cr_number='<?php echo escape($p->cr_number); ?>'
								data-from_service='<?php echo escape($from_service); ?>'
								data-cr_date='<?php echo date('m/d/Y',$p->cr_date); ?>'
								>
								Show Data
								</button>
									<?php if(!$is_agent){
											?>
										<button class='btn btn-danger btnRemoveCR' data-cr_number='<?php echo escape($p->cr_number); ?>'>Remove</button>
									<?php } ?>
									<?php } ?>
								</td>
					</tr>
						<?php
						}
					?>

					<?php
				}
			?>
			</tbody>
			</table>
			<?php
		}else{
			echo "<div class='alert alert-info'>No record yet.</div>";
		}
	}

	function depositByCr($cr_number){
		$user_number = new User_credit();
		$n = 0;
		if($cr_number){
			$result = $user_number->depositCrDeposit($cr_number);
			if(isset($result->total) && $result->total){
				$n = $result->total;
			}
		}
		return $n;
	}

	function updateCrDate(){
		$date_old = Input::get('date_old');
		$cr_number_old = Input::get('cr_number_old');
		$date = Input::get('date');
		$override = Input::get('override');
		if(!$override){
		 $payment = new Payment();
		 $payment->updateCrDate($cr_number_old,$date);
		} else {
		 $payment = new Payment();
		 $payment->updateCrDateOverride($cr_number_old,$date);
		}

		$user = new User();
		Log::addLog($user->data()->id,$user->data()->company_id,"CR List Update Date $cr_number_old","ajax_accounting.php");

		//echo $date_old . " " . $date . " " . $cr_number_old ;

	}
	function getSavedDeposits($cr_num){
				$deposit = new User_credit();
				$list_deposits =  $deposit->getCRDepositDetails($cr_num);
				$rows = "";
				$total = 0;
				$config_commission = Configuration::getValue('ar_commissionable');
				if($list_deposits){

					foreach($list_deposits as $dep){
						$dt_created = date('m/d/y',$dep->dt_created);
						$row = "";
						$row .= "<tr class='bg-warning'>";
						$row .= "<td style='border-top:1px solid #ccc;'>$dt_created</td>";
						$row .= "<td style='border-top:1px solid #ccc;'>Deposit</td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'>$dep->member_name</td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$dep->amount</td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$total += $dep->amount;
						if($config_commission == 1){
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
						}
						  $row .= "</tr>";
						  $rows .=$row;
					}
				}
				return ['rows' => $rows, 'total' => $total];
}
	function getSavedOtherIncome($cr_num){
				$other_income = new Other_income();
				$list_incomes =  $other_income->getByCr($cr_num);
				$rows = "";
				$total = 0;
				$config_commission = Configuration::getValue('ar_commissionable');
				if($list_incomes){

					foreach($list_incomes as $dep){
						$dt_created = date('m/d/y',$dep->created);
						$source = $dep->member_name;
						if($dep->other_source){
							$source = $dep->other_source;
						}
						$row = "";
						$row .= "<tr class='bg-warning'>";
						$row .= "<td style='border-top:1px solid #ccc;'>$dt_created</td>";
						$row .= "<td style='border-top:1px solid #ccc;'>Other Income</td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'>$source</td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$dep->amount</td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$row .= "<td style='border-top:1px solid #ccc;'></td>";
						$total += $dep->amount;
						if($config_commission == 1){
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
							$row .= "<td style='border-top:1px solid #ccc;'></td>";
						}
						  $row .= "</tr>";
						  $rows .=$row;
					}
				}
				return ['rows' => $rows, 'total' => $total];
}
	function collectionReport(){
		$date1 = Input::get('dt1');
		$date2 = Input::get('dt2');
		$salestype = Input::get('salestype');
		$terminal_id = Input::get('terminal_id');
		$user_id = Input::get('user_id');
		$agent_id = Input::get('agent_id');
		$paid_by = Input::get('paid_by');
		$type = Input::get('type');
		$cr_num = Input::get('cr_num');
		$from_service = Input::get('from_service');
		$show_with_cr = Input::get('show_with_cr');
		$cr_include_dr = Input::get('cr_include_dr');
		$cr_include_ir = Input::get('cr_include_ir');
		$payment = new Payment();
		$arr_records = [];
		$with_log = false;
		$cur_time = time();
		$diff_from_now = $cur_time - strtotime($date1);
		$user = new User();
		$is_agent = $user->hasPermission('wh_agent');
		$commissionable_days_allowed = 60;
		$config_commission = Configuration::getValue('ar_commissionable');
		$one_year = 31536000;

		$allow_old = ($diff_from_now > $one_year) ? true : false;

		//$monthsallowed = 5184000;

		$monthsallowed = 1296000;

		$payment_ids_arr = [];

		if(!$date1){
			if(!$cr_num){
				$date1 = date('m/d/Y');
				$date2 = strtotime($date1 . " 1 day -1 sec");
				$date1 = strtotime($date1);
			}
		} else {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2 . " 1 day -1 sec");
		}

		if($cr_num){
			$cr_log = new Cr_log();
			$cr_list = $cr_log->getByCR($cr_num);

			if($cr_list){ //$cr_list
				$with_log = true;
				$totalReceiptAmountLog = 0;
				$totalDeductionLog=0;
				$totalCollectedAmountLog=0;
				$approved = true;
				$is_status_one = false;
				foreach($cr_list as $cr){

					$btnForApproval = "";

					if(!$is_agent && $config_commission == 1){
						$payment_info = $cr->payment_info;
						if($payment_info){
							$payment_info = json_decode($payment_info);
							if($payment_info->payment_id){
								$cr_log_mem_credit = new Member_credit();
								$cr_log_mem_credit_type = $cr_log_mem_credit->getMemberCreditByPaymentID($payment_info->payment_id);
								if($cr_log_mem_credit_type->status == -1 ){

									$btnForApproval = "<a  href='#' style='font-size:20px;' title='Approve' data-id='". Encryption::encrypt_decrypt('encrypt',$cr_log_mem_credit_type->id)."' class='btnApproveCredit'><i class='fa fa-check'></i></a>";
									$btnForApproval .= " <a  href='#' style='font-size:20px;' title='Decline' data-id='". Encryption::encrypt_decrypt('encrypt',$cr_log_mem_credit_type->id)."' class='btnDeclineCredit text-danger'><i class='fa fa-remove'></i></a>";
									$approved = false;

									if ($payment_info->type != 'cash'){
										$btnForApproval .= " <a  href='#' style='font-size:20px;' title='Update' data-cr_log_id='".$cr->id."' data-pid='".$payment_info->payment_id."'  data-id='".$payment_info->id."'  data-method='".$payment_info->type."' class='btnUpdateCredit text-primary'><i class='fa fa-pencil'></i></a>";
									}
								}
							}
						}
					}

					if($cr->status == 1){
						$is_status_one  = true;
					}

					$row = "";
					$row .= "<tr class='bg-warning'>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->delivery_date </td>";
					$row .= "<td style='border-top:1px solid #ccc;'> $cr->delivery_receipt </td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->sales_invoice</td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->client_name</td>";
					$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$cr->receipt_amount</td>";
					$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$cr->deduction</td>";
					$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$cr->paid_amount</td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->bank_name</td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->check_no</td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->check_date</td>";
					$row .= "<td style='border-top:1px solid #ccc;'>$cr->terms</td>";

					if($config_commission == 1){

						$row .= "<td style='border-top:1px solid #ccc;'>$cr->commission</td>";
						$row .= "<td style='border-top:1px solid #ccc;'>$cr->freight</td>";
						$row .= "<td style='border-top:1px solid #ccc;'>$cr->ar_number</td>";
						$row .= "<td style='border-top:1px solid #ccc;'>$cr->cr_slip_number $btnForApproval</td>";
						
					}

					$row .= "</tr>";
					$totalReceiptAmountLog+= str_replace(",","",$cr->receipt_amount);
					$totalDeductionLog+= str_replace(",","",$cr->deduction);
					$totalCollectedAmountLog+= str_replace(",","",$cr->paid_amount);

					$arr_records[] = ['row' => $row,'dr'=> $cr->delivery_receipt,'check_num' => $cr->check_no, 'type' => 0];
					// edit and approve
				}

			/*
				usort($arr_records, function($a, $b)
				{
					 $rdiff = $a['type'] - $b['type'];
				    if ($rdiff) return $rdiff;
				       return strcmp($a['check_num'], $b['check_num']);

				   // return strcmp($a['check_num'], $b['check_num']);
				});
			*/

				?>

				<div class="row">
				<div class="col-md-6"></div>
				<div class="col-md-6 text-right">
					<button id='btnExcelCollectionReport' class='btn btn-default'><i class='fa fa-download'></i> Download</button>
					<button id='btnPrintCollectionReport' class='btn btn-default'><i class='fa fa-print'></i> Print</button>
				</div>
				</div>


				<?php
				echo "<p>Showing payments from <span class='text-danger'>".date('F d, Y H:i:s A',$date1)."</span> to  <span class='text-danger'>".date('F d, Y  H:i:s A',$date2)."</span></p>";
				if($is_status_one && $approved && !$is_agent){
				echo "<div><button class='btn btn-primary' data-cr_number='$cr_num' id='btnApproveAgentCR'>Finalize Collection Report</button></div>";
				}

				echo "<div id='table-collection-report'>";
				echo "<table  class='table table-bordered'>";
				echo "<thead><tr><th>Delivery Date</th><th>Delivery Receipt</th><th>Sales Invoice</th><th>Name of Client</th><th class='text-right'>Receipt amount</th><th class='text-right'>Deduction</th><th class='text-right'>Check/Cash Amount</th><th>Bank Name</th><th>Check No.</th><th>Check Date</th><th>Terms</th>";
				if($config_commission == 1){
					echo "<th>Com</th>";
					echo "<th>Freight</th>";
					echo "<th>AR</th>";
					echo "<th>Slip #</th>";
				}
				echo "</tr></thead>";
				echo "<tbody>";
				$cr_log_ctr = 0;
				 foreach($arr_records as $arr_record){
				    echo $arr_record['row'];
				    $cr_log_ctr++;
				 }
				// show deposits
				$savedDeposits = getSavedDeposits($cr_num);

				if($savedDeposits['rows'] && $savedDeposits['total']){
					$totalCollectedAmountLog += $savedDeposits['total'];
					echo $savedDeposits['rows'];

				}
				// show other incomes
				$savedOtherIncomes = getSavedOtherIncome($cr_num);

				if($savedOtherIncomes['rows'] && $savedOtherIncomes['total']){
					$totalCollectedAmountLog += $savedOtherIncomes['total'];
					echo $savedOtherIncomes['rows'];

				}
				echo "</tbody>";
				echo "<tfoot><th></th><th></th><th></th><th></th><th class='text-right'>".number_format($totalReceiptAmountLog,2)."</th><th class='text-right'>".number_format($totalDeductionLog,2)."</th><th class='text-right'>".number_format($totalCollectedAmountLog,2)."</th><th></th><th></th><th></th><th></th>";
				if($config_commission == 1){
					echo "<th></th>";
					echo "<th></th>";
					echo "<th></th>";
					echo "<th></th>";
				}
				echo "</tfoot>";
				echo "</table>";

				$pages = ceil(count($cr_log_ctr) / 22);

				echo "<div class='row'><div class='col-md-6'><p>Number of page(s): $pages</p> </div><div class='col-md-6 text-right'><input type='text' id='overrided_item_per_page' value='22'><span class='help-block'>Override Items Per Page</span></div></div>";

			}
		}

		if(!$with_log){
			$payments = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num,$from_service,$paid_by,$show_with_cr,'','',$agent_id);
			if($cr_include_dr || $cr_include_ir){
				$payments2 = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num,$from_service,$paid_by,$show_with_cr,$cr_include_dr,$cr_include_ir,$agent_id);
			}
			if($payments2 && is_array($payments2)){
				$payments = array_merge($payments,$payments2);
			}

			if($payments){


				?>
				<div class="row">
					<div class="col-md-3">
						<input type="text" id='crNumber' class="form-control" placeholder="ENTER CR NUMBER">
						<span class='help-block'>CR NUMBER</span>
					</div>
					<div class="col-md-3">
						<input type="text" id='crDate' class="form-control" value="<?php echo date('m/d/Y'); ?>" placeholder="ENTER CR DATE">
						<span class='help-block'>CR DATE</span>
					</div>
					<div class="col-md-3">
						 <button id='btnSaveCRNumber' class="btn btn-default" type="button"><i class='fa fa-save'></i> SAVE CR</button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
					<?php
						if(Configuration::thisCompany('cebuhiq')){
							?>
							<button id='btnHideUncheck' style='z-index:999;display:none;position:fixed;bottom:10px;left:10px;opacity: 0.9;' class='btn btn-danger'>Hide Uncheck Records</button>
							<?php
						}
					?>
					</div>
					<div class="col-md-6">
						<div class='text-right'>
						<button id='btnPrintCollectionReport' class='btn btn-default'><i class='fa fa-print'></i> Print</button>

								<?php if (!$is_agent){
								?>
							<button id='btnExcelCollectionReport' class='btn btn-default'><i class='fa fa-download'></i> Download</button>
							<div><input type="checkbox" id='hideDeduction'> <label for="hideDeduction">Hide deduction column on download</label></div>
								<?php
								}?>

						</div>
						<br>
					</div>

				</div>

				<?php

				$totalCollectedAmount = 0;
				$totalReceiptAmount = 0;
				$totalDeduction = 0;
				$totalCommission= 0;
				$totalFreight= 0;
				$page_ctr =0;
				$payment_ids = [];


				$all_payment_ids = "";
				foreach($payments as $pm){
					$all_payment_ids .= $pm->id .",";
				}
				$all_payment_ids = rtrim($all_payment_ids,",");

				$cr_log_ids_cls = new Cr_log_ids();
				$cr_log_ids_check = $cr_log_ids_cls->getByPaymentIds($all_payment_ids);
				$cr_log_ids_arr = [];
				if($cr_log_ids_check){
					foreach($cr_log_ids_check as $log_ids){
						$cr_log_ids_arr[$log_ids->payment_id][$log_ids->payment_type][$log_ids->type_id] = $log_ids->cr_number;
					}
				}

				$all_payments_list = getPaymentsOrderIds($all_payment_ids);

				foreach($payments as $pm){

					//$payment_list = getPaymentsOrder($pm->id);
					//$arr_payments = json_decode($payment_list,true);
					$arr_payments = isset($all_payments_list[$pm->id]) ? $all_payments_list[$pm->id] : [];
					$total = number_format($pm->stotal,2);



					$deduction = 0;
					$cash = 0;
					$amount_lbl = "";
					$bank_lbl = "";
					$cheque_number_lbl = "";
					$date_lbl = "";
					$terms = 0;
					$freight = ($pm->freight_charge) ? $pm->freight_charge: 0;
					$totalFreight += $freight;
					$cur_total = 0;
					$total_cc = 0;
					$cc_perc = 0.035;
					$lblwarn ='';
					if(trim($pm->cr_number)){
						$lblwarn = "bg-warning";
					}
					//if($pm->cr_number && !$show_with_cr) continue;

					if($pm->is_scheduled){
						$date_sold = date('m/d/y',$pm->is_scheduled);
					} else {

						$date_sold = date('m/d/y',$pm->sold_date);

					}
					$lbl_from_service="";
					if($pm->from_service){
					$lbl_from_service ="SVC";
					}
					$payment_method_arr = [];

					if(isset($pm->terms)){
						$terms = $pm->terms;
					}



					if(isset($arr_payments['cash'])){

							if($type && !in_array(1,$type)){

							} else {
								$cash = 0;
								$cash_id = 0;
								foreach($arr_payments['cash'] as $arr_cash){

									if($arr_cash['id'] == '2464' ) continue;

										if(isset($cr_log_ids_arr[$pm->id]['cash'][$arr_cash['id']])){
											continue;
										}

										$createdAt = $arr_cash['created'];
										if(($createdAt < $date1 || $createdAt > $date2 ) && !$cr_num && !$allow_old) continue;
										$cash += $arr_cash['amount'];
										$cur_total += $arr_cash['amount'];
										$totalCollectedAmount+= $arr_cash['amount'];
										$payment_ids_arr[$pm->id][] = ['type' => 'cash', 'id' => $arr_cash['id']];
										$cash_id = $arr_cash['id'];
								}

								if($cash){
									$cash = number_format($cash,2);
									$amount_lbl .= "<span class='span-block'><span class='pull-left'>Cash:</span> $cash</span>";
									$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
									$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
									$date_lbl .= "<span class='span-block'>&nbsp;</span>";
									$payment_method_arr[] = [
									'type' => 1,
									'lbl' => "$cash",
									'pid' => $pm->id,
									'method' => "cash",
									'raw_date' =>'',
									'id' => $cash_id,
									'bank' => "<span class='span-block'>&nbsp;</span>",
									'check_num' => "",
									'date' => "<span class='span-block'>&nbsp;</span>"
									];
								}

							}



					}
					/*if(isset($arr_payments['mem_credit'])){
						$mem_credit = $arr_payments['mem_credit']['amount']-$arr_payments['mem_credit']['amount_paid'];
						$mem_credit = number_format($mem_credit,2);
						$amount_lbl .= "<span class='span-block'>Credit: $mem_credit</span>";
						$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
						$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
						$date_lbl .= "<span class='span-block'>&nbsp;</span>";
					}*/
					if(isset($arr_payments['credit'])){


						if($type && !in_array(2,$type)){

						} else {
							foreach($arr_payments['credit'] as $arr_cheque){

								if(isset($cr_log_ids_arr[$pm->id]['credit'][$arr_cheque['id']])){
									continue;
								}


								$createdAt = $arr_cheque['created'];

								$dif_cre = $cur_time - $createdAt;
								if($dif_cre > $monthsallowed ){
									$allow_old = true;
								} else {
									$allow_old = false;
								}

								if(($createdAt < $date1 || $createdAt > $date2) && !$allow_old) continue;

								$cur_total += ($arr_cheque['amount'] - (ROUND($arr_cheque['amount'] * $cc_perc,2))) ;
								$total_cc += $arr_cheque['amount'];
								$cheque = number_format($arr_cheque['amount'],2);
								$amount_lbl .= "<span class='span-block'><span class='pull-left'>CC:</span> $cheque</span>";
								$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
								$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[approval_code]</span>";
								$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
								$totalCollectedAmount+= $arr_cheque['amount'];

								$payment_ids_arr[$pm->id][] = ['type' => 'credit', 'id' => $arr_cheque['id']];

								$payment_method_arr[] =[
									'type' => 2,
									'lbl' => "$cheque",
									'method' => "credit",
									'pid' => $pm->id,
									'raw_date' =>'',
									'id' => $arr_cheque['id'],
									'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
									'check_num' => "$arr_cheque[approval_code]",
									'date' => "<span class='span-block'>$arr_cheque[date]</span>"
									];

								$terms = $arr_cheque['trace_number'];
							}
						}

					}
					if(isset($arr_payments['bt'])){

						if($type && !in_array(4,$type)){

						} else {
							foreach($arr_payments['bt'] as $arr_cheque){


								if($arr_cheque['id'] == 1710) continue;

								if(isset($cr_log_ids_arr[$pm->id]['bt'][$arr_cheque['id']])){
									continue;
								}


								$createdAt = $arr_cheque['created'];
								$dif_cre = $cur_time - $createdAt;
								if($dif_cre > $monthsallowed ){
									$allow_old = true;
								}else {
									$allow_old = false;
								}

								if(($createdAt < $date1 || $createdAt > $date2) && !$allow_old) continue;

								$cur_total += $arr_cheque['amount'];
								$cheque = number_format($arr_cheque['amount'],2);
								$amount_lbl .= "<span class='span-block'><span class='pull-left'>BT:</span> $cheque</span>";
								$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
								$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[ref_number]</span>";
								$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
								$totalCollectedAmount+= $arr_cheque['amount'];

								$payment_ids_arr[$pm->id][] = ['type' => 'bt', 'id' => $arr_cheque['id']];

								$payment_method_arr[] =[
								'type' => 4,
								'lbl' => "$cheque",
								'method' => "bt",
								'pid' => $pm->id,
								'id' => $arr_cheque['id'],
								'raw_date' =>'',
								'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
								'check_num' => "$arr_cheque[ref_number]",
								'date' => "<span class='span-block'>$arr_cheque[date]</span>"
								];
							}
						}

					}

					if(isset($arr_payments['cheque'])){

						if($type && !in_array(3,$type)){

						} else {
							foreach($arr_payments['cheque'] as $arr_cheque){

								if(isset($cr_log_ids_arr[$pm->id]['cheque'][$arr_cheque['id']])){
									continue;
								}

								$createdAt = $arr_cheque['created'];
								$dif_cre = $cur_time - $createdAt;

								if($dif_cre > $monthsallowed ){
									$allow_old = true;
								}else {
									$allow_old = false;
								}

								$to_now_show = [3741,5521,3740,2758,4,2757,7604,3249,2865];

								if(in_array($arr_cheque['id'],$to_now_show)) continue;

								if(($createdAt < $date1 || $createdAt > $date2 )  && !$allow_old) continue;

								$cur_total += $arr_cheque['amount'];
								$cheque = number_format($arr_cheque['amount'],2);
								$amount_lbl .= "<span class='span-block'><span class='pull-left'>Cheque:</span> $cheque</span>";
								$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
								$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[ref_number]</span>";
								$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
								$totalCollectedAmount+= $arr_cheque['amount'];

								$payment_ids_arr[$pm->id][] = ['type' => 'cheque', 'id' => $arr_cheque['id']];

								$payment_method_arr[] = [
									'type' => 3,
									'lbl' => "$cheque",
									'method' => "cheque",
									'pid' => $pm->id,
									'id' => $arr_cheque['id'],
									'raw_date' => $arr_cheque['date'],
									'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
									'check_num' => "$arr_cheque[ref_number]",
									'date' => "<span class='span-block'>$arr_cheque[date]</span>"
								];

							}
						}

					}

					$arr_deductions = [];
					$has_deductions = false;
					if(isset($arr_payments['deduction'])){
						foreach($arr_payments['deduction'] as $ind_deduction){
							$deduct = $ind_deduction['amount'];
							$deduction += $deduct;
							if(Configuration::thisCompany('cebuhiq')){
								$has_deductions = true;
							}

							$arr_deductions[] = $deduct;

							$payment_ids_arr[$pm->id][] = ['type' => 'deduction', 'id' => $ind_deduction['id']];

						}
					}


					if(Configuration::thisCompany('cebuhiq')){
							if(!$amount_lbl && $pm->stotal != $deduction) continue; // NO payment: cash, credit card, bank transfer, cheque
					} else {
							if(!$amount_lbl) continue; // NO payment: cash, credit card, bank transfer, cheque
					}




					//	$deduction = number_format($cc_perc * $total_cc,2,".","");
					//go1



					if(isset($arr_payments['consumable'])){

						foreach($arr_payments['consumable'] as $ind_deduction){
							$deduct = $ind_deduction['amount'];
							$deduction += $deduct;
							$arr_deductions[] = $deduct;
							$payment_ids_arr[$pm->id][] = ['type' => 'consumable', 'id' => $ind_deduction['id']];
						}
					}
						$btnForApproval = "";
						$has_pending_for_approval = 0;
					if(isset($arr_payments['mem_credit'])){
						//backhere

						$deduct = $arr_payments['mem_credit']['amount']  - $arr_payments['mem_credit']['amount_paid'] ;
						$deduction += $deduct;
						$arr_deductions[] = $deduct;
						if(!$is_agent && $arr_payments['mem_credit']['status'] == -1){
							$btnForApproval = "<a  href='#' style='font-size:20px;' title='Approve' data-id='". Encryption::encrypt_decrypt('encrypt',$arr_payments['mem_credit']['id'])."' class='btnApproveCredit'><i class='fa fa-check'></i></a>";
							$btnForApproval .= " <a  href='#' style='font-size:20px;' title='Decline' data-id='". Encryption::encrypt_decrypt('encrypt',$arr_payments['mem_credit']['id'])."' class='btnDeclineCredit text-danger'><i class='fa fa-remove'></i></a>";

							$has_pending_for_approval = 1;
						} else if ($is_agent && $arr_payments['mem_credit']['status'] == -1){
							$btnForApproval = "For Approval";
							$has_pending_for_approval = 1;

						}



					}

					$totalReceiptAmount += $pm->stotal;
					$totalDeduction +=  (float) number_format($deduction,2,".","");



					$dr_pr_label = "";
					$invoice_label = "";
					if($pm->dr){
						$dr_pr_label = $pm->dr;
					} else if($pm->ir){
						$dr_pr_label = $pm->ir;
					} else {
						$dr_pr_label ='N/A';
					}
					if($pm->invoice){
						$invoice_label = $pm->invoice;
					} else {
						$invoice_label = "N/A";
					}

					$categ_label ='';

					if($_SERVER['HTTP_HOST'] == 'vitalite.apollosystems.com.ph' || $_SERVER['HTTP_HOST'] == 'localhost:81'){
						$arr_categ = [86,87,88,89,11];
						if(in_array($pm->category_id,$arr_categ)){
							$categ_label = "Gal.";
						}
					}


					$row = "";
					$first_deduction = '';
					if(count($payment_method_arr) == 1){
						if(count($arr_deductions)){
							$first_deduction = 0;
							foreach($arr_deductions as $adeduct){
								$first_deduction += $adeduct;
							}
						}

					} else {
						$first_deduction = isset($arr_deductions[0]) ? $arr_deductions[0] :  0;
					}
					if($payment_method_arr || $has_deductions){
						$temp_dr = trim($dr_pr_label);
					$temp_invoice = trim($invoice_label);


					$temp_amount = str_replace(",","",$payment_method_arr[0]['lbl']);
					$temp_check_num = trim($payment_method_arr[0]['check_num']);


					$temp_cr_log = new Cr_log();
					$checker = $temp_cr_log->checkCR($temp_dr,$temp_invoice,$temp_amount,$temp_check_num,$cr_num,$pm->lastname);
					$has_main_amount = false;

					if($checker && !$show_with_cr && !$cr_num){
						$totalDeduction = $totalDeduction - $first_deduction;
						$totalCollectedAmount = $totalCollectedAmount - $temp_amount;
						$totalReceiptAmount = $totalReceiptAmount - $total;

					} else {

						if($cr_num && !$checker){
							//$totalDeduction = $totalDeduction - $first_deduction;
							//$totalCollectedAmount = $totalCollectedAmount - $temp_amount;
							//$totalReceiptAmount = $totalReceiptAmount - $total;

						} else {
						$has_main_amount = true;
						$checker = ($checker) ? 1 : 0;
						}
						$col_td_com = "";
						if($config_commission == 1){ // if config has commission is set to yes
							if(isset($payment_method_arr[0]['raw_date']) && $payment_method_arr[0]['raw_date']){

								$is_valid_commissionables = time() - strtotime($payment_method_arr[0]['raw_date']);

							} else {
								$is_valid_commissionables = time() - strtotime($date_sold);

							}
							$day = 86400;
							$com_amount = 0;
							if( ($commissionable_days_allowed * $day) > $is_valid_commissionables){
								$has_commission = "with commision";
								$com_amount = $payment_method_arr[0]['lbl'];
							} else {
								$has_commission = "without commision";
								$com_amount = 0;
							}
							if (!$is_agent && $arr_payments['mem_credit']['status'] == -1 && $payment_method_arr[0]['method'] != 'cash'){
								$btnForApproval .= " <a  href='#' style='font-size:20px;' title='Update' data-pid='".$payment_method_arr[0]['pid']."'  data-id='".$payment_method_arr[0]['id']."'  data-method='".$payment_method_arr[0]['method']."' class='btnUpdateCredit text-primary'><i class='fa fa-pencil'></i></a>";

							}
							$col_td_com = "<td  style='border-top:1px solid #ccc;' class='text-right'>$com_amount</td><td style='border-top:1px solid #ccc;' class='text-right'>$freight</td><td  style='border-top:1px solid #ccc;' class='text-right'><input type='text' placeholder='AR #' style='width:80px;display:block;margin-bottom:3px;' class='txt_ar'> <input type='text' placeholder='CR #' style='width:80px;display:block;margin-bottom:3px;'  class='txt_cr'></td><td style='border-top:1px solid #ccc;'>$btnForApproval</td>";
							$totalCommission += (1 * str_replace(',','',$com_amount));
						}
							$chkBatch = "";
							if(Configuration::thisCompany('cebuhiq')){
								$chkBatch = "<br><br><input type='checkbox' style='display:block' class='chkBatch'>";
							}

							$row .= "<tr $checker   class='$lblwarn' data-pid='".$payment_method_arr[0]['pid']."'  data-id='".$payment_method_arr[0]['id']."'  data-method='".$payment_method_arr[0]['method']."' >";
							$row .= "<td style='border-top:1px solid #ccc;'>$date_sold</td>";
							$row .= "<td style='border-top:1px solid #ccc;'>$dr_pr_label $categ_label $lbl_from_service</td>";
							$row .= "<td style='border-top:1px solid #ccc;'>$invoice_label $categ_label $lbl_from_service</td>";
							$row .= "<td style='border-top:1px solid #ccc;'>$pm->lastname</td>";
							$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$total</td>";
							$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$first_deduction</td>";
							$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>" . $payment_method_arr[0]['lbl'] ."</td>"; //$amount_lbl
							$row .= "<td style='border-top:1px solid #ccc;'>" . $payment_method_arr[0]['bank'] ."</td>"; // $bank_lbl
							$row .= "<td style='border-top:1px solid #ccc;'>" . $payment_method_arr[0]['check_num'] ."</td>"; // $cheque_number_lbl
							$row .= "<td style='border-top:1px solid #ccc;'>" . $payment_method_arr[0]['date'] ."</td>"; // $date_lbl
							$row .= "<td style='border-top:1px solid #ccc;'>$terms</td>";
							$row .= $col_td_com;
							$row .="<td style='border-top:1px solid #ccc;'><i style='cursor:pointer' class='fa fa-close btnRemoveTransaction' title='Remove from CR'> </i>$chkBatch</td>";
							$row .= "</tr>";

							$arr_records[] = ['row' => $row,'dr'=>$dr_pr_label,'type' => $payment_method_arr[0]['type'],'check_num' => $payment_method_arr[0]['check_num']];


					}

					if(count($payment_method_arr) > 1){
						$ctr = 0;
						foreach($payment_method_arr as $parr){
							$ctr++;
							if($ctr == 1){
								continue;
							}

							$arr_deduct = isset($arr_deductions[$ctr-1]) ? $arr_deductions[$ctr-1] : '';

							$temp_amount = str_replace(",","",$parr['lbl']);
							$temp_check_num = trim($parr['check_num']);
							$temp_cr_log = new Cr_log();
							$checker = $temp_cr_log->checkCR($temp_dr,$temp_invoice,$temp_amount,$temp_check_num,$pm->lastname);

							if($checker && !$show_with_cr && !$cr_num){
								$totalDeduction = $totalDeduction - $arr_deduct;
								$totalCollectedAmount = $totalCollectedAmount -  $temp_amount;

							} else {
								if($cr_num && !$checker){
								//	$totalDeduction = $totalDeduction - $arr_deduct;
								//	$totalCollectedAmount = $totalCollectedAmount -  $temp_amount;
								} else {
								$tmp_total ="";
								if(!$has_main_amount){
									$tmp_total = $total;
								}
								}

								$col_td_com = "";
								if($config_commission == 1){ // if config has commission is set to yes
									if(isset($parr[0]['raw_date']) && $parr[0]['raw_date']){

										$is_valid_commissionables = time() - strtotime($parr[0]['raw_date']);

									} else {
										$is_valid_commissionables = time() - strtotime($date_sold);

									}
									$day = 86400;

									if( ($commissionable_days_allowed * $day) > $is_valid_commissionables){
										$has_commission = "with commision";
										$com_amount = $parr[0]['lbl'];
									} else {
										$has_commission = "without commision";
										$com_amount = 0;
									}
									$col_td_com = "<td  style='border-top:1px solid #ccc;' class='text-right'>$com_amount</td><td  style='border-top:1px solid #ccc;' class='text-right'></td><td style='border-top:1px solid #ccc;' class='text-right'>$freight</td><td style='border-top:1px solid #ccc;' class='text-right'></td>";
									$totalCommission += (1 * str_replace(',','',$com_amount));
								}

							$chkBatch = "";
							if(Configuration::thisCompany('cebuhiq')){
								$chkBatch = "<input type='checkbox' style='display:block' class='chkBatch'>";
							}
								$row = "";
								$row .= "<tr class='' data-id='$parr[id]' data-pid='$parr[pid]' data-method='$parr[method]' >";
								$row .= "<td style='border-top:1px solid #ccc;'>$date_sold</td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$dr_pr_label $categ_label $lbl_from_service</td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$invoice_label $categ_label $lbl_from_service</td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$pm->lastname</td>";
								$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$tmp_total</td>";
								$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$arr_deduct</td>";
								$row .= "<td style='border-top:1px solid #ccc;' class='text-right'>$parr[lbl]</td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$parr[bank] </td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$parr[check_num]</td>";
								$row .= "<td style='border-top:1px solid #ccc;'>$parr[date]</td>";
								$row .= "<td style='border-top:1px solid #ccc;'></td>";
								$row .= $col_td_com;
								$row .= "<td style='border-top:1px solid #ccc;'><i style='cursor:pointer' class='fa fa-close btnRemoveTransaction' title='Remove from CR'></i> $chkBatch</td>";
								$row .= "</tr>";
								$arr_records[] = [ 'row' => $row, 'dr'=>$dr_pr_label, 'type' => $parr['type'],'check_num' =>  $parr['check_num']];


							}


						}
					}


					$payment_ids[] = $pm->id;
					}

				}

				$bydr = false;
				if(count($type) == 1 && $type[0] == 1){
					$bydr = true;
				}


				usort($arr_records, function($a, $b) use($bydr)
				{
					if($bydr){

                        return $a['dr'] >  $b['dr'];

					} else {

						 $rdiff = $a['type'] - $b['type'];
					    if ($rdiff) return $rdiff;
					       return strcmp($a['check_num'], $b['check_num']);
					}


				   // return strcmp($a['check_num'], $b['check_num']);
				});

				echo "<p>Showing payments from <span class='text-danger'>".date('F d, Y H:i:s A',$date1)."</span> to  <span class='text-danger'>".date('F d, Y  H:i:s A',$date2)."</span></p>";
				echo "<p>Click <i class='fa fa-remove'></i> to remove them from the list</p>";
				echo "<input type='hidden' value='$has_pending_for_approval' id='has_pending_for_approval'>";

				echo "<div id='table-collection-report'>";
				echo "<table  class='table table-bordered'>";
				$com_col= "";
				$com_tfoot="";
				if($config_commission == 1){
					$com_col = "<th>Com</th><th>Freight</th><th><th></th></th>";
					$com_tfoot = "<th class='text-right' id='footer_com'>".number_format($totalCommission,2)."</th>";
					$com_tfoot .="<th class='text-right'>".number_format($totalFreight,2)."</th>";
					$com_tfoot .="<th class='text-right'></th>";
					$com_tfoot .="<th class='text-right'></th>";
				}
				echo "<thead><tr><th>Delivery Date</th><th>Delivery Receipt</th><th>Sales Invoice</th><th>Name of Client</th><th class='text-right'>Receipt amount</th><th class='text-right'>Deduction</th><th class='text-right'>Check/Cash Amount</th><th>Bank Name</th><th>Check No.</th><th>Check Date</th><th>Terms</th>$com_col <th></th></tr></thead>";
				echo "<tbody>";
				$total_rows = 0;
				foreach($arr_records as $arr_record){
				    echo $arr_record['row'];
				    $total_rows++;
				 }

				echo "</tbody>";
				echo "<tfoot><th></th><th></th><th></th><th></th><th class='text-right' id='footer_receipt_amount'>".number_format($totalReceiptAmount,2)."</th><th class='text-right' id='footer_deduction'>".number_format($totalDeduction,2)."</th><th class='text-right' id='footer_collected_amount'>".number_format($totalCollectedAmount,2)."</th><th></th><th></th><th></th><th></th>$com_tfoot<th></th></tfoot>";
				echo "</table>";


				$pages = ceil($total_rows / 22);
				echo "<div class='row'><div class='col-md-6'><p id='page_warning' data-total='$total_rows'>Total transactions: $total_rows Number of CR(s): $pages</p> </div><div class='col-md-6 text-right'><input type='text' id='overrided_item_per_page' value='22'><span class='help-block'>Override Items Per Page</span></div></div>";
				echo "</div>";
				echo "<input type='hidden' value='".json_encode($payment_ids)."' id='cr_payment_ids'>";
				echo "<input type='hidden' value='".json_encode($payment_ids_arr)."' id='cr_log_ids'>";

			//	dump($payment_ids_arr);

			// get deposits/overpayment
			if(Configuration::getValue('deposits_collection') == 1){
				$deposit = new User_credit();

				//$list_deposits =  $deposit->getDeposits($date1,$date2);

				//showDeposits($list_deposits);
			}



			} else {
				echo "<div class='alert alert-info'>No result found</div>";
			}
		}
	}

	function showDeposits($list_deposits){

		if($list_deposits){
			?>
			<hr >

			<div id="no-more-tables">
			<table class='table table-bordered table-condensed' id='tblDeposits'>
			<thead>
			<tr>
				<TH>ID</TH>
				<TH>Member</TH>
				<TH>Payment</TH>
				<TH>Data</TH>
				<th>Added by</th>
				<TH>Status</TH>
				<th>Created</th>
				<th>Add To CR</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($list_deposits as $o){
				$types = ['','Cash','Credit Card','Check','Bank Transfer'];
					$used_arr  = ['Not Used','Used'];
					$type_name = isset($types[$o->status]) ? $types[$o->status] : 'Unknown payment';
					$payment_ids = "";
					$datalbl = "";
					$remaininglbl = "";
					$temp_holder = [];
					$used_total = $o->used_total;
					if($o->status == 1){
						$datalbl .= "Amount: " . $o->json_data;
						$remaininglbl .= "Remaining " . number_format(($o->json_data - $used_total),2);

					} else if ($o->status == 2){
						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Card: " .$c->card_type."</p>";
							$datalbl .= "<p>Trance Number: " .$c->trace_number."</p>";
							$datalbl .= "<p>Date: " .$c->date."</p>";
							$datalbl .= "<p>Amount: " .$c->amount."</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
						}
						$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
					} else if ($o->status == 3){

						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Ctrl#: " .$c->cheque_number."</p>";
							$datalbl .= "<p>Date: " .$c->date."</p>";
							$datalbl .= "<p>Amount: " .$c->amount."</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
							$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
						}

					} else if ($o->status == 4){
						$temp_holder = json_decode($o->json_data);
						$total = 0;
						if($temp_holder){
							foreach($temp_holder as $c){
								$datalbl .= "<p>Ctrl#: " . $c->credit_number ."</p>";
							$datalbl .= "<p>Date: " . $c->date. "</p>";
							$datalbl .= "<p>Bank: " . $c->bank_name . "</p>";
							$datalbl .= "<p>Amount: " . $c->amount. "</p>";
							$datalbl .= "<hr>";
							$total = $total +$c->amount;
							}
							$datalbl .= "Total : " . number_format($total,2);
							$remaininglbl .= "Remaining " . number_format(($total - $used_total),2);
						}
					}
					$ret_use_payment = "";
					if($o->payment_id){
						$payment_arr = json_decode($o->payment_id,true);
						if($payment_arr){
							foreach($payment_arr as $aa){
								$sales = new Sales();
								$det = $sales->getsinglesale($aa['payment_id']);
								$cur="";
								if(isset($det->invoice) && $det->invoice){
									$cur .= " <span class='label label-info'>Inv: $det->invoice</span> ";
								}

								if(isset($det->dr) && $det->dr){
									$cur .= " <span class='label label-info'>DR:  $det->dr</span> ";
								}

								if(isset($det->ir) && $det->ir){
									$cur .= " <span class='label label-info'>PR: $det->ir</span> ";
								}

								if($aa['amount']){
									$cur .= " <span class='label label-warning'>Paid Amount: " . number_format($aa['amount'],2) . "</span> ";
								}
								$ret_use_payment .= $payment_arr = "<span class='span-block'>$cur</span>";

							}
						}
					}

					?>
				<tr>
						<td><strong><?php echo escape($o->id); ?></strong></td>
						<td><strong><?php echo escape($o->lastname); ?> 		<span class='span-block'><?php echo $ret_use_payment; ?></span></strong>
							<span class='text-muted span-block'><?php echo escape($o->remarks); ?></span>
						</td>
						<td><?php echo escape($type_name); ?></td>
						<td>
							<?php echo $datalbl; ?>
							<span class='span-block'><?php echo "Used: " . number_format($o->used_total,2); ?></span>
							<span class='span-block'><?php echo $remaininglbl; ?></span>

						</td>
						<td><?php echo capitalize($o->ufn . " " . $o->uln); ?></td>
						<td class='text-danger'><?php echo $used_arr[$o->is_used]; ?></td>
						<td><?php echo date('F d, Y H:i:s A',$o->created); ?></td>
						<td><input type="checkbox" value='<?php echo $o->id; ?>' class='chkDeposit'></td>
					</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			</div>
			<?php
		}
	}

	function sortByCheckNum($x, $y) {
		return   $x['check_num'] -  $y['check_num'];
	}

	function updateCrNumber(){
		$ids = Input::get('payment_ids');
		$ids = json_decode($ids,true);
		$crNum = Input::get('crNumber');
		$type = Input::get('type');
		$dt = Input::get('dt');
		$dt_to = Input::get('dt_to');
		$user_id = Input::get('user_id');
		$paid_by = Input::get('paid_by');
		$cr_include_dr = Input::get('cr_include_dr');
		$cr_include_ir = Input::get('cr_include_ir');
		$from_service = Input::get('from_service');
		$cr_override = Input::get('cr_override');
		$agent_id = Input::get('agent_id');
		$crDate = Input::get('crDate');

		$cr_log_ids = Input::get('cr_log_ids');
		$cr_log_ids = json_decode($cr_log_ids,true);

		$arr_deposits =  Input::get('arr_deposits');
		$arr_deposits =  json_decode($arr_deposits,true);
		$arr_dep_id ='';
		if($arr_deposits && count($arr_deposits)){
			$arr_dep_id = implode(',',$arr_deposits);
			$user_deposit = new User_credit();
			$user_deposit->updateCredit($arr_dep_id,$crNum);

		}



		$cr_override = ($cr_override) ? $cr_override : 22;
		if(!is_numeric($cr_override)) $cr_override =22;

		$det_arr = Input::get('det_arr');

		$det_arr = json_decode($det_arr);
		$cr_log = new Cr_log();
		$temp_cr = $crNum;
		$temp_ctr= 1;

		$exploded_cr_num =[];
		if(strpos($crNum,",") > 0){
			$exploded_cr_num = explode(",",$crNum);

		}




		if($det_arr){
			$cr_log_id_cls = new Cr_log_ids();
			$det_crnum = $crNum;
			$user = new User();
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"ADD CR NUMBER $crNum",
				'ajax_accounting.php'
			);

			$det_index =0;
			$is_agent = $user->hasPermission('wh_agent');
			$is_save_by_agent = ($is_agent) ? 1 : 0;
			foreach($det_arr as $det){

				$det->receipt_amount = str_replace(',','',$det->receipt_amount);
				$det->deduction = str_replace(',','',$det->deduction);
				$det->paid_amount = str_replace(',','',$det->paid_amount);

				if($exploded_cr_num){
					$det_crnum = $exploded_cr_num[$det_index];
				}

				$checker = $cr_log->isExistsCR(
					$det_crnum,
					$det->delivery_date,
					$det->delivery_receipt,
					$det->sales_invoice,
					$det->client_name,
					$det->receipt_amount,
					$det->deduction,
					$det->paid_amount,
					$det->bank_name,
					$det->check_no,
					$det->check_date,
					$det->terms
				);

				if(!$checker){
					$arr_cr_payment_json = [];
					if($det->pid && $det->method && $det->id & $det->id){
						$cr_log_id_cls->create(
							[
							'cr_number' => $det_crnum,
							'payment_id' => $det->pid,
							'payment_type' => $det->method,
							'type_id' => $det->id,
							'created'=> time()
							]
						);
						$arr_cr_payment_json = ['payment_id'=>$det->pid,'id'=>$det->id,'type'=>$det->method];

					}

					$comm = (isset($det->comm) && $det->comm) ? $det->comm : 0;
					$ar_number = (isset($det->ar_number) && $det->ar_number) ? $det->ar_number : 0;
					$cr_slip = (isset($det->cr_slip) && $det->cr_slip) ? $det->cr_slip : 0;

					$cr_log->create([
						'cr_number' => $det_crnum,
						'delivery_date' => $det->delivery_date,
						'delivery_receipt' => $det->delivery_receipt,
						'sales_invoice' => $det->sales_invoice,
						'client_name' => $det->client_name,
						'receipt_amount' => $det->receipt_amount,
						'deduction' => $det->deduction,
						'paid_amount' => $det->paid_amount,
						'bank_name' => $det->bank_name,
						'check_no' => $det->check_no,
						'check_date' => $det->check_date,
						'terms' => $det->terms,
						'commission' => $comm,
						'ar_number' => $ar_number,
						'cr_slip_number' => $cr_slip,
						'status' => $is_save_by_agent,
						'payment_info' => json_encode($arr_cr_payment_json),
						]
					);

					if($det->pid){
						$payment = new Payment($det->pid);
						if($crDate){
							$now = strtotime($crDate);
							if(!$now){
								$now = time();
							}
						} else {
							$now = time();
						}

						if(isset($payment->data()->id)&& $payment->data()->id){
							$cur_cr_number = $payment->data()->cr_number;
							if(strpos($cur_cr_number,$det_crnum)  === false){
								$cur_cr_number = $cur_cr_number ."," . $det_crnum;
								$cur_cr_number = trim($cur_cr_number,",");
							}
							$payment->update(['cr_number' => $cur_cr_number,'cr_date' =>$now ],$det->pid);
						}
					}



					if($temp_ctr % $cr_override == 0){
						$det_crnum +=1;
						$det_index++;
					}
					$temp_ctr++;

				}
			}

		}




		$dt = ($dt) ? strtotime($dt) : time();
		$dt_to = ($dt_to) ? strtotime($dt_to) : time();
		$user_id = ($user_id)? $user_id : 0;
		$paid_by = ($paid_by)? $paid_by : 0;
		$from_service = ($from_service)? $from_service : 0;

		$arr_of_cr = [];
			$index = 0;
			$ctr = 1;
		foreach($ids as $id){
			$arr_of_cr[$index][] = $id;
			if($ctr % $cr_override == 0){
				$index++;
			}
			$ctr++;
		}

		/*
		for($i=0;$i<=$index;$i++){
			$ids = implode(',',$arr_of_cr[$i]);
			$payment = new Payment();

			$payment->updateCROfPayment($crNum,$ids);
			$crNum += 1;
		}
		*/
		$u_cr = $crNum;

		for($i=0;$i<=$index;$i++){
			$ids = $arr_of_cr[$i];
			if($exploded_cr_num){
				$u_cr= $exploded_cr_num[$i];
			}
			/*
			foreach($ids as $ind){
				$ind_payment = new Payment($ind);

				if($ind_payment->data()->cr_number != ''){
					$newcrnum = $ind_payment->data()->cr_number . ","  . $u_cr;
				} else {
					$newcrnum = $u_cr;
				}
				$now = time();

				$ind_payment->update(['cr_number' => $newcrnum,'cr_date' => $now],$ind);

			} */

			$collection_report = new Collection_report();

			// check if exists
			// check info , append when necessarry
			// add service

			$tipo = json_encode($type);
			$collection_report->create(
				[
					'ref_id' => $u_cr,
					'remarks' =>  $tipo,
					'created' => $dt,
					'to_date' => $dt_to,
					'receive_by' => $paid_by,
					'cashier_id' => $user_id,
					'include_ov' => $cr_include_dr,
					'include_dv' => $cr_include_ir,
					'is_service' => $from_service,
					'agent_id' => $agent_id
				]
			);
			if(!$exploded_cr_num){
				$u_cr += 1;
			}

		}
		echo "Updated successfully.";

	}
	function excelCR(){

		$filename = "collection-report-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		echo Input::get('table');


	}
	function excelCollectionReport(){

		$filename = "collection-report-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$date1 = Input::get('dt1');
		$date2 = Input::get('dt2');

		$salestype = json_decode(Input::get('salestype'),true);
		$terminal_id = Input::get('terminal_id');
		$user_id = Input::get('user_id');
		$type = json_decode(Input::get('type'),true);
		$cr_num = Input::get('cr_num');
		$from_service = Input::get('from_service');
		$show_with_cr = Input::get('show_with_cr');
		$hideDeduction = Input::get('hideDeduction');


		$payment = new Payment();
		if(!$date1){
			$date1 = date('F Y');
			$date2 = strtotime($date1 . " 1 day -1 sec");
			$date1 = strtotime($date1);
		} else {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2 . " 1 day -1 sec");
		}

		$payments = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num,$from_service,0,$show_with_cr);

		if($payments){
			echo "<table border='1' class='table table-bordered'>";
			echo "<thead><tr><th>Delivery Date</th><th>Delivery Receipt</th><th>Sales Invoice</th><th>Name of Client</th><th class='text-right'>Receipt amount</th>";

			if(!$hideDeduction) echo "<th class='text-right'>Deduction</th>";

			echo "<th class='text-right'>Check/Cash Amount</th><th>Bank Name</th><th>Check No.</th><th>Check Date</th><th>Terms</th></tr></thead>";
			echo "<tbody>";
			$totalCollectedAmount = 0;
			$totalReceiptAmount = 0;
			$totalDeduction = 0;
			$all_payment_ids = "";
			foreach($payments as $pm){
				$all_payment_ids .= $pm->id .",";
			}
			$all_payment_ids = rtrim($all_payment_ids,",");
			$all_payments_list = getPaymentsOrderIds($all_payment_ids);

			foreach($payments as $payment){
				//$payment_list = getPaymentsOrder($payment->id);

				//$arr_payments = json_decode($payment_list,true);
				$arr_payments = isset($all_payments_list[$payment->id]) ? $all_payments_list[$payment->id] : [];
				$total = number_format($payment->stotal,2);
				$deduction = 0;
				$cash = 0;
				$amount_lbl = "";
				$bank_lbl = "";
				$cheque_number_lbl = "";
				$date_lbl = "";
				$terms = 0;
				$cur_total = 0;
				$total_cc = 0;
				$cc_perc = 0.035;
				$payment_method_arr = [];
				if($payment->is_scheduled){
					$date_sold = date('m/d/y   ',$payment->is_scheduled);
				} else {
					if($payment->from_od == 1){
						$date_sold = 'N/A';
					} else {
						$date_sold = date('m/d/y',$payment->sold_date);
					}
				}
				$lbl_from_service="";
				if($payment->from_service){
				$lbl_from_service ="SVC";
				}
				if(isset($arr_payments['cash'])){
					$cash = 0;



						if(is_array($type) && $type && !in_array(1,$type)){

						} else {
							foreach($arr_payments['cash'] as $arr_cash){
								$createdAt = $arr_cash['created'];
								if($createdAt < $date1 || $createdAt > $date2) continue;
								$cash += $arr_cash['amount'];
								$cur_total += $arr_cash['amount'];
								$totalCollectedAmount += $arr_cash['amount'];
							}
							$cash = number_format($cash,2);
							$amount_lbl .= "<span class='span-block'><span class='pull-left'>Cash:</span> $cash</span>";
							$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
							$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
							$date_lbl .= "<span class='span-block'>&nbsp;</span>";

							$payment_method_arr[] = [
							'lbl' => "$cash",
							'bank' => "<span class='span-block'>&nbsp;</span>",
							'check_num' => "",
							'date' => "<span class='span-block'>&nbsp;</span>"
							];
						}

				}

				/*

				    if(isset($arr_payments['mem_credit'])){
					$mem_credit = $arr_payments['mem_credit']['amount']-$arr_payments['mem_credit']['amount_paid'];
					$mem_credit = number_format($mem_credit,2);
					$amount_lbl .= "<span class='span-block'>Credit: $mem_credit</span>";
					$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
					$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
					$date_lbl .= "<span class='span-block'>&nbsp;</span>";

				}*/

				if(isset($arr_payments['credit'])){

				if(is_array($type) && $type && !in_array(2,$type)){

					} else {
						foreach($arr_payments['credit'] as $arr_cheque){
							$cur_total += $arr_cheque['amount'];
							$total_cc += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "<span class='span-block'><span class='pull-left'>CC:</span> $cheque</span>";
							$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
							$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[ref_number]</span>";
							$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
							$totalCollectedAmount += $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];
					}

				}
				if(isset($arr_payments['bt'])){

					if(is_array($type) && $type && !in_array(4,$type)){

					} else {
						foreach($arr_payments['bt'] as $arr_cheque){


							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "<span class='span-block'><span class='pull-left'>BT:</span> $cheque</span>";
							$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
							$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[ref_number]</span>";
							$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
							$totalCollectedAmount += $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];
					}

				}
				if(isset($arr_payments['cheque'])){

					if(is_array($type) && $type && !in_array(3,$type)){

					} else {
						foreach($arr_payments['cheque'] as $arr_cheque){
							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "<span class='span-block'><span class='pull-left'>Cheque:</span> $cheque</span>";
							$bank_lbl .= "<span class='span-block'>$arr_cheque[bank]</span>";
							$cheque_number_lbl .= "<span class='span-block'>$arr_cheque[ref_number]</span>";
							$date_lbl .= "<span class='span-block'>$arr_cheque[date]</span>";
							$totalCollectedAmount += $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];

					}

				}
				if(isset($payment->terms)){
					$terms = $payment->terms;
				}
				if(!$amount_lbl) continue;
//point2


				//$deduction = number_format($cc_perc * $total_cc,2,".","");
				if(isset($arr_payments['deduction'])){
					foreach($arr_payments['deduction'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;
					}
				}
				if(isset($arr_payments['consumable'])){

					foreach($arr_payments['consumable'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;
					}

				}
				if(isset($arr_payments['mem_credit'])){
					$deduct = $arr_payments['mem_credit']['amount']  - $arr_payments['mem_credit']['amount_paid'] ;
					$deduction += $deduct;
				}

				$totalReceiptAmount += $payment->stotal;
				$totalDeduction +=  (float) number_format($deduction,2,".","");





				$dr_pr_label = "";
				$invoice_label = "";
				$categ_label ='';

				if( $_SERVER['HTTP_HOST'] == 'vitalite.apollosystems.com.ph' || $_SERVER['HTTP_HOST'] == 'localhost:81' ){
					$arr_categ = [86,87,88,89,11];

					if(in_array($payment->category_id,$arr_categ)){
						$categ_label = "Gal.";
					}
				}

				if($payment->dr){
					$dr_pr_label = $payment->dr;
				} else if($payment->ir){
					$dr_pr_label = $payment->ir;
				} else {
					$dr_pr_label ='N/A';
				}

				if($payment->invoice){
					$invoice_label = $payment->invoice;
				} else {
					$invoice_label = "N/A";
				}

				echo "<tr>";
				echo "<td style='border-top:1px solid #ccc;'>$date_sold</td>";
				echo "<td style='border-top:1px solid #ccc;'>$dr_pr_label $categ_label $lbl_from_service</td>";
				echo "<td style='border-top:1px solid #ccc;'>$invoice_label $categ_label $lbl_from_service</td>";
				echo "<td style='border-top:1px solid #ccc;'>$payment->lastname</td>";
				echo "<td style='border-top:1px solid #ccc;' class='text-right'>$total</td>";
				if(!$hideDeduction) echo "<td style='border-top:1px solid #ccc;' class='text-right'>$deduction</td>";
				echo "<td style='border-top:1px solid #ccc;' class='text-right'>".$payment_method_arr[0]['lbl']."</td>";
				echo "<td style='border-top:1px solid #ccc;'>".$payment_method_arr[0]['bank']."</td>";
				echo "<td style='border-top:1px solid #ccc;'>".$payment_method_arr[0]['check_num']."</td>";
				echo "<td style='border-top:1px solid #ccc;'>".$payment_method_arr[0]['date']."</td>";
				echo "<td style='border-top:1px solid #ccc;'>$terms</td>";
				echo "</tr>";
				if(count($payment_method_arr) > 0){
					$first = true;
					foreach($payment_method_arr as $pma){
						if($first){
							$first = false;
							continue;
						}
							echo "<tr>";
						echo "<td style='border-top:1px solid #ccc;'></td>";
						echo "<td style='border-top:1px solid #ccc;'></td>";
						echo "<td style='border-top:1px solid #ccc;'></td>";
						echo "<td style='border-top:1px solid #ccc;'></td>";
						echo "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						if(!$hideDeduction) echo "<td style='border-top:1px solid #ccc;' class='text-right'></td>";
						echo "<td style='border-top:1px solid #ccc;' class='text-right'>$pma[lbl]</td>";
						echo "<td style='border-top:1px solid #ccc;'>$pma[bank]</td>";
						echo "<td style='border-top:1px solid #ccc;'>$pma[check_num]</td>";
						echo "<td style='border-top:1px solid #ccc;'>$pma[date]</td>";
						echo "<td style='border-top:1px solid #ccc;'></td>";
						echo "</tr>";
					}
				}
			}
			echo "</tbody>";
			echo "<tfoot><th></th><th></th><th></th><th></th><th class='text-right'>".number_format($totalReceiptAmount,2)."</th>";
			if(!$hideDeduction) echo "<th class='text-right'>".number_format($totalDeduction,2)."</th>";
			echo "<th class='text-right'>".number_format($totalCollectedAmount,2)."</th><th></th><th></th><th></th><th></th></tfoot>";
			echo "</table>";
		} else {
			echo "<div class='alert alert-info'>No result found</div>";
		}

	}
	function getPaymentsOrderIds($pids){
		$cash = new Cash();
		$credit = new Credit();
		$cheque = new Cheque();
		$bt = new Bank_transfer();
		$con = new Payment_consumable();
		$conFree = new Payment_consumable_freebies();
		$member_credit = new Member_credit();
		$deduction = new Deduction();

		$payment_arr = [];

		$cash_list = $cash->getByPids($pids);
		$credit_list = $credit->getByPids($pids);
		$cheque_list = $cheque->getByPids($pids);
		$bt_list = $bt->getByPids($pids);
		$con_list = $con->getByPids($pids);
		$conFree_list = $conFree->getByPids($pids);
		$member_credit_list = $member_credit->getByPids($pids);
		$deductions = $deduction->getByPids($pids);


		if($cash_list){
			foreach($cash_list as $c){
				$payment_arr[$c->payment_id]['cash'][] = ['created'=> $c->created,'id' => $c->id,'amount' => $c->amount,'date' =>date('m/d/Y',$c->created)];
			}
		}
		if($credit_list){
			foreach($credit_list as $c){
				$payment_arr[$c->payment_id]['credit'][] = [
					'id' =>$c->id,
					'created' => $c->created,
					'amount' =>$c->amount,
					'bank' =>$c->bank_name,
					'ref_number' =>$c->card_number,
					'approval_code' =>$c->approval_code,
					'trace_number' =>$c->trace_number,
					'ref_number' =>$c->card_number,
					'date' =>date('m/d/y',$c->date)
				];

			}
		}
		if($cheque_list){
			foreach($cheque_list as $c){
					$payment_arr[$c->payment_id]['cheque'][] = [
					'id' =>$c->id,
					'created' => $c->created,
					'amount' =>$c->amount,
					'bank' =>$c->bank,
					'ref_number' =>$c->check_number,
					'date' =>date('n/j/y',$c->payment_date)
				];
			}
		}
		if($bt_list){
			foreach($bt_list as $c){
					$payment_arr[$c->payment_id]['bt'][] = [
					'id' =>$c->id,
					'amount' =>$c->amount,
					'created' => $c->created,
					'bank' =>$c->bankfrom_name,
					'ref_number' =>$c->bankfrom_account_number,
					'date' =>date('n/j/y',$c->date)
				];
			}
		}

		if($con_list){
			foreach($con_list as $c){
				$payment_arr[$c->payment_id]['con']['id'] = $c->id;
				$payment_arr[$c->payment_id]['con']['amount'] = $c->amount;
				$payment_arr[$c->payment_id]['con']['date'] = '';

			}
		}
		if($conFree_list){
			foreach($conFree_list as $c){
				$payment_arr[$c->payment_id]['conf']['id'] = $c->id;
				$payment_arr[$c->payment_id]['conf']['amount'] = $c->amount;
				$payment_arr[$c->payment_id]['conf']['date'] = date('m/d/Y',$c->created);
			}
		}
		if($member_credit_list){
			foreach($member_credit_list as $c){
				$payment_arr[$c->payment_id]['mem_credit']['id'] = $c->id;
				$payment_arr[$c->payment_id]['mem_credit']['amount'] = $c->amount;
				$payment_arr[$c->payment_id]['mem_credit']['amount_paid'] = $c->amount_paid;
				$payment_arr[$c->payment_id]['mem_credit']['date'] =  $c->created;
				$payment_arr[$c->payment_id]['mem_credit']['status'] =  $c->status;
			}
		}
		if($deductions){
			foreach($deductions as $c){
				$payment_arr[$c->payment_id]['deduction'][] = ['id' => $c->id,'amount'=> $c->amount,'created'=> $c->created];
			}
		}
		return $payment_arr;
	}
	function getPaymentsOrder($payment_id){
		$id = $payment_id;
		$cash = new Cash();
		$credit = new Credit();
		$cheque = new Cheque();
		$bt = new Bank_transfer();
		$con = new Payment_consumable();
		$conFree = new Payment_consumable_freebies();
		$member_credit = new Member_credit();
		$deduction = new Deduction();


		$cash_list = $cash->get_active('cash',array('payment_id','=',$id));
		$credit_list = $credit->get_active('credit_card',array('payment_id','=',$id));
		$cheque_list = $cheque->get_active('cheque',array('payment_id','=',$id));
		$bt_list = $bt->get_active('bank_transfer',array('payment_id','=',$id));
		$con_list = $con->get_active('payment_consumable',array('payment_id','=',$id));
		$conFree_list = $conFree->get_active('payment_consumable_freebies',array('payment_id','=',$id));
		$member_credit_list = $member_credit->get_active('member_credit',array('payment_id','=',$id));
		$deductions = $deduction->get_active('deductions',array('payment_id','=',$id));

		$arr=[];
		if($deductions){
			foreach($deductions as $c){
				/*$arr['deduction']
				$arr['deduction']['amount'] = $c->amount;
				$arr['deduction']['created'] = $c->created;*/
				$arr['deduction'][] = ['id' => $c->id,'amount'=> $c->amount,'created'=> $c->created];
			}
		}
		if($con_list){
			foreach($con_list as $c){

				/*
					$arr['consumable']['id'] = $c->id;
					$arr['consumable']['amount'] = $c->amount;
					$arr['consumable']['created'] = $c->created;
				*/

				$arr['consumable'][] = ['id' => $c->id, 'amount' => $c->amount, 'created' => $c->created];

			}
		}
		if($cash_list){
			foreach($cash_list as $c){
				$arr['cash'][] = ['created'=> $c->created,'id' => $c->id,'amount' => $c->amount,'date' =>date('m/d/Y',$c->created)];

			}
		}
		if($credit_list){
			foreach($credit_list as $c){
				$arr['credit'][] = [
					'id' =>$c->id,
					'created' => $c->created,
					'amount' =>$c->amount,
					'bank' =>$c->bank_name,
					'ref_number' =>$c->card_number,
					'approval_code' =>$c->approval_code,
					'trace_number' =>$c->trace_number,
					'ref_number' =>$c->card_number,
					'date' =>date('m/d/y',$c->date)
				];
			}
		}
		if($cheque_list){
			foreach($cheque_list as $c){
				$arr['cheque'][] = [
					'id' =>$c->id,
					'created' => $c->created,
					'amount' =>$c->amount,
					'bank' =>$c->bank,
					'ref_number' =>$c->check_number,
					'date' =>date('n/j/y',$c->payment_date)
				];

			}
		}
		if($bt_list){
			foreach($bt_list as $c){
				$arr['bt'][] = [
					'id' =>$c->id,
					'amount' =>$c->amount,
					'created' => $c->created,
					'bank' =>$c->bankfrom_name,
					'ref_number' =>$c->bankfrom_account_number,
					'date' =>date('n/j/y',$c->date)
				];
			}
		}
		if($con_list){
			foreach($con_list as $c){
				$arr['con']['id'] = $c->id;
				$arr['con']['amount'] = $c->amount;
				$arr['con']['date'] = '';

			}
		}
		if($conFree_list){
			foreach($conFree_list as $c){
				$arr['conf']['id'] = $c->id;
				$arr['conf']['amount'] = $c->amount;
				$arr['conf']['date'] = date('m/d/Y',$c->created);
			}
		}
		if($member_credit_list){
			foreach($member_credit_list as $c){
				$arr['mem_credit']['id'] = $c->id;
				$arr['mem_credit']['amount'] = $c->amount;
				$arr['mem_credit']['amount_paid'] = $c->amount_paid;
				$arr['mem_credit']['date'] =  $c->created;
				$arr['mem_credit']['status'] =  $c->status;
			}
		}
		return json_encode($arr);
	}


function testPrintPdf(){

			$order_id = Input::get('order_id');

			$myOrder  = new Wh_order($order_id);
			$whorder = new Wh_order_details();
			$order_details = $myOrder->getFullDetails($order_id);
			$finalarr = [];
			$type = 2;
			if($type == 1){ // invoice
				$ctr_num = $order_details->invoice;
			} else if ($type == 2){
				$ctr_num = $order_details->dr;
			} else if ($type == 3){
				$ctr_num = $order_details->pr;
			}


			$date_sold = "06/01/2017";
			$client_name = $order_details->mln;
			$client_address = $order_details->personal_address;
			$terms = $order_details->terms;
			$sales_type = $order_details->sales_type_name;
			$po_num = $order_details->po_num;
			$tin = $order_details->tin_no;
			$remarks = $order_details->remarks;
			$branch_name = $order_details->branch_name;
			$cashier = ucwords($order_details->ufn . " "  . $order_details->uln);
			$items = [];


			$orders = $whorder->getOrderDetails($order_id);

			foreach($orders as $order){
				$desc = strtolower($order->description);
				$total = ($order->qty * $order->adjusted_price) + $order->member_adjustment;
				$indDiscount = $order->member_adjustment / $order->qty;
				$adjustedPrice = $order->adjusted_price + $indDiscount;

				$racking = $order->racking;
				/*
							$items[] = [
								'original_price' => $order->adjusted_price,
								'unit_name'=>escape($order->unit_name),
								'item_code'=>escape($order->item_code),
								'description'=>strtolower($desc),
								'barcode'=>escape($order->barcode),
								'qty'=>escape(formatQuantity($order->qty)),
								'price'=>escape($adjustedPrice),
								'discount'=>escape($order->member_adjustment),
								 'total'=>escape($total),
								 'racking' => $rack_json];
				*/
				$items[] = [formatQuantity($order->qty),$order->unit_name,$desc,number_format($adjustedPrice,2),number_format($total,2)];
			}

			$per_page = 15;
			$line_height = 7;
			$new_line = ($per_page - count($items)) * $line_height;


			$pdf=new Pdf_sales();
			$pdf->AddPage();
			$pdf->SetFont('Arial','',14);

			//Side Content
			$pdf->SetWidths(array(150,30));
			$pdf->Row(array("",$ctr_num));
			$pdf->Row(array("",$order_id));
			$pdf->Row(array("",$date_sold));

			// 2 col

			$pdf->SetWidths(array(90,90));
			$pdf->Row(array($client_name,$client_address));

			// 3 col
			$pdf->SetWidths(array(60,60,60));
			$pdf->Row(array($terms,$tin,$sales_type));

			//item width
			$pdf->SetWidths(array(20,15,105,25,25));
			$pdf->ln(10);
			$pdf->SetAligns(array("L","L","L","R","R"));
			foreach($items as $item){
					$pdf->Row(array($item[0],$item[1],$item[2],$item[3],$item[4]));
			}
			$pdf->SetAligns([]);
			$pdf->ln($new_line);
			$pdf->SetWidths(array(180));
			$pdf->Row(array($cashier));
			if($remarks){
				$pdf->Row(array($remarks));
			}

			$pdf->Row(array($branch_name));
			if($po_num){
				$pdf->Row(array($po_num));
			}

			$pdf->Row(array(date('m/d/Y H:i:s A')));

			$pdf->Output();

}

function printMe(){

			include "../includes/fancyrow.php";

			$pdf = new PDF_FancyRow();
			$pdf->AddPage();
			$pdf->SetMargins(5,0,0);

			$pdf->SetFont('Arial', '', 12);
			$pdf->ln(1);
			$arr_format = [];
			$arr_format[0] = ['col'=> 3,'borders' =>['B','B','B'],'col_widths' => [120,40,40], 'values'=>['1','2','3']];
			$arr_format[1] = ['col'=> 3,'borders' =>['B','B','B'],'col_widths' => [120,40,40], 'values'=>['1','2','3']];
			$arr_format[10] = ['col'=> 1,'borders' =>['B'],'col_widths' => [200], 'values'=>['tsdfj slkdfj slkdfjs lkd']];
			for($i=0 ; $i<53;$i++){
					$caption = [];
					$border = [];
					$align = [];
					if(isset($arr_format[$i])){
						$widths = $arr_format[$i]['col_widths'];
						 $empty = $arr_format[$i]['values'];
						 $border = $arr_format[$i]['borders'];
					} else {
					   $widths = array(200);
						$border = ['B'];
					   $empty = array($i);
					}

				$pdf->SetWidths($widths);

					$pdf->FancyRow($empty, $border);
					$pdf->FancyRow($caption, $empty, $align);
			}

			$pdf->Output();

}

	function samplePrint(){
		require('../libs/fpdf17/fpdf.php');

		$member_id = Input::get('member_id');
		$soa_cancel = Input::get('soa_cancel');
		$soa_fully_paid = Input::get('soa_fully_paid');

		if(is_numeric($member_id)){
			$user= new User();
			$comp = new Company($user->data()->company_id);
			$company_name = $comp->data()->name;
			$address_company =$comp->data()->address;
			$addtl_info = $comp->data()->description;
			$pdf = new PDF($company_name,0,$address_company,$addtl_info,$comp->data()->contact_number);
			$mem_cls = new Member($member_id);
			$acc_name = $mem_cls->data()->lastname;
			$salesman = "N/A";
			if($mem_cls->data()->salestype){
				$type = new Sales_type($mem_cls->data()->salestype);
				$salesman = $type->data()->name;
			}
			$address = $mem_cls->data()->personal_address;
			$terms = $mem_cls->data()->terms;
			$date = date('F d, Y');

			$pdf->SetFont('Arial','',8);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->Ln(5);


			//$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,0,'Account Name: ',0,0,'L',false);
			$pdf->Cell(0,0,$acc_name,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell(20,0,'Sales man: ',0,0,'L',false);
			$pdf->Cell(0,0,$salesman,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell(20,0,'Address: ',0,0,'L',false);
			$pdf->Cell(0,0,$address,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell(20,0,'Terms: ',0,0,'L',false);
			$pdf->Cell(0,0,$terms,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell(20,0,'Date: ',0,0,'L',false);
			$pdf->Cell(0,0,$date,0,0,'L',false);
			$pdf->Ln(5);
			$textBody = '';
			$arraydata =[];
			$ar_member = $mem_cls->salesByType(0, 123, 123,$member_id,0,0,$soa_cancel,$soa_fully_paid,1);
			$total_per_member = 0;
			$total_sold = 0;
			$total_paid = 0;
			$total_freight = 0;
			if($ar_member) {



				foreach($ar_member as $ar) {



					// ** START
						$pending_amount = $ar->pending_amount - $ar->amount_paid;
						//$total_amount = $pending_amount + $ar->valid_cheque + $ar->invalid_cheque;
						//$amount_paid = $ar->amount_paid +  $ar->valid_cheque + $ar->invalid_cheque;
						$total_amount = $ar->pending_amount;
						$amount_paid = $ar->amount_paid;
						//if(!$pending_amount) continue;



						$member_terms = ($ar->terms) ? $ar->terms : 'N/A';
						if($ar->is_scheduled){
							$date_sched = date('m/d/Y',$ar->is_scheduled);
						} else {
							$date_sched = ($ar->sold_date) ? date('m/d/Y',$ar->sold_date) : 'N/A';
						}
						$freight = $ar->freight_charge ? $ar->freight_charge : 0;

						$freight_lbl = "";
						if($freight){
							$freight_lbl .= "<small class='span-block text-danger'>Freight: " . number_format($freight,2). "</small>";

						}
						$bg = "";
						$fp = "";
						if($ar->status == 1){
							$bg = "bg-danger";
						}

						if(!$pending_amount){
							$fp = "<small class='span-block text-danger'>Fully Paid</small>";
						}
						if(!$ar->pending_amount){
							$pending_amount = 0;
							$amount_paid = $ar->saletotal;
							$total_amount =  $ar->saletotal;
						}
					// ** end

					$dr_num = DR_PREFIX.$ar->dr;
					$pr_num = $ar->ir;
					$dr_date = $date_sched;
					$amt_due = number_format($total_amount, 2);
					$inv_no = $ar->invoice;
					$po_no= $ar->client_po;
					$freight = $ar->freight_charge ? $ar->freight_charge : 0;


					$amt_paid = number_format($amount_paid, 2);
					$balance = number_format($pending_amount+$freight, 2);
					$total_freight += $freight;
					$days = getTime(time() - strtotime($date_sched));

					$freight_lbl = "0";
					if($freight){
						$freight_lbl =  number_format($freight,2);
					}

					$total_per_member += ($pending_amount+$freight);
					$total_sold += $total_amount;
					$total_paid += $amount_paid;
					$arraydata[] = array($dr_num,$pr_num,$dr_date,$amt_due ,$freight_lbl,$inv_no,$po_no,$amt_paid,$balance,$days);

				}
			}

/*
			$this->Cell($w[0],10,'Dr Num',1,0,'C',false);
			$this->Cell($w[1],10,'Dr Date',1,0,'C',false);
			$this->Cell($w[2],10,'Amt Due',1,0,'C',false);
			$this->Cell($w[3],10,'Inv Num',1,0,'C',false);
			$this->Cell($w[4],10,'P.O. Num',1,0,'C',false);
			$this->Cell($w[5],10,'Amt Paid',1,0,'C',false);
			$this->Cell($w[6],10,'Balance',1,0,'C',false);
			$this->Cell($w[7],10,'Days',1,0,'C',false);
*/

			$header = [DR_LABEL,PR_LABEL,'Dr Date', 'Amt Due','Freight','Inv Num','P.O num','Amt Paid','Balance','Days'];
			$widths = array(16,16, 20,22,18,16,25,20,20,10);
			$data = $pdf->LoadData($arraydata);

			$total_sold = number_format($total_sold,2);
			$total_paid = number_format($total_paid,2);
			$total_per_member = number_format($total_per_member,2);
			$total_freight = number_format($total_freight,2);

			$pdf->ImprovedTable($header,$widths,$data);
			//$total_sold,$total_paid,$total_per_member
			$left_margin = 120;
			$pdf->Cell(183,5,'',1,0,'C',false);
			$pdf->Ln(15);
			$pdf->Cell($left_margin,0,'',0,0,'L',false);
			$pdf->Cell(40,0,'Total Amount Sold: ',0,0,'L',false);
			$pdf->Cell(0,0,$total_sold,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell($left_margin,0,'',0,0,'L',false);
			$pdf->Cell(40,0,'Total Freight: ',0,0,'L',false);
			$pdf->Cell(0,0,$total_freight,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell($left_margin,0,'',0,0,'L',false);
			$pdf->Cell(40,0,'Total Amount Paid: ',0,0,'L',false);
			$pdf->Cell(0,0,$total_paid,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell($left_margin,0,'',0,0,'L',false);
			$pdf->Cell(40,0,'Net Balance: ',0,0,'L',false);
			$pdf->Cell(0,0,$total_per_member,0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Cell(40,0,'Prepared by:  ______________________ ',0,0,'L',false);
			$pdf->Cell(0,0,'',0,0,'L',false);
			$pdf->Ln(7);

			$pdf->Cell(40,0,'Received by: ______________________ ',0,0,'L',false);
			$pdf->Cell(0,0,'',0,0,'L',false);
			$pdf->Ln(5);
			$pdf->Output();
		}
	}
	function printCollectionReport(){
		require('../libs/fpdf17/fpdf.php');

		$date1 = Input::get('dt1');
		$date2 = Input::get('dt2');
		$salestype = Input::get('salestype');
		$terminal_id = Input::get('terminal_id');
		$user_id = Input::get('user_id');
		$type = Input::get('type');
		$cr_num = Input::get('cr_num');

		$payment = new Payment();
		if(!$date1){
			$date1 = date('F Y');
			$date2 = strtotime($date1 . " 1 day -1 sec");
			$date1 = strtotime($date1);
		} else {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2 . " 1 day -1 sec");
		}
		$payments = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num);
		if($payments){
			$user= new User();
			$comp = new Company($user->data()->company_id);
			$company_name = $comp->data()->name;
			$address_company =$comp->data()->address;
			$addtl_info = $comp->data()->description;
			$pdf = new PDF($company_name,1,$address_company,$addtl_info);

			$pdf->SetFont('Arial','',8);
			$pdf->AliasNbPages();
			$pdf->AddPage('L');
			$pdf->Ln(5);


			//$pdf->SetFont('Arial','',10);
			/*$pdf->Cell(20,0,'Account Name: ',0,0,'L',false);
			$pdf->Cell(0,0,$acc_name,0,0,'L',false);
			$pdf->Ln(5); */
			$totalCollectedAmount = 0;
			$totalReceiptAmount = 0;
			$totalDeduction = 0;

			$all_payment_ids = "";
			foreach($payments as $pm){
				$all_payment_ids .= $pm->id .",";
			}
			$all_payment_ids = rtrim($all_payment_ids,",");
			$all_payments_list = getPaymentsOrderIds($all_payment_ids);

			foreach($payments as $payment){
				//$payment_list = getPaymentsOrder($payment->id);

				//$payment_list
				//$payment_list
				$arr_payments = isset($all_payments_list[$payment->id]) ? $all_payments_list[$payment->id] : [];
				$total = $payment->stotal;
				$deduction = 0;
				$cash = 0;
				$amount_lbl = "";
				$bank_lbl = "";
				$cheque_number_lbl = "";
				$date_lbl = "";
				$terms = 0;
				$cur_total = 0;
				$total_cc = 0;
				$cc_perc = 0.035;
				$payment_method_arr = [];
				if($payment->is_scheduled){
					$date_sold = date('m/d/Y',$payment->is_scheduled);
				} else {
					$date_sold = 'N/A';
				}

				if(isset($arr_payments['cash'])){
					$cash = 0;


					if($type && !in_array(1,$type)){

					} else {
						foreach($arr_payments['cash'] as $arr_cash){
								$cash += $arr_cash['amount'];
								$cur_total += $arr_cash['amount'];
								$totalCollectedAmount +=  $arr_cash['amount'];
						}
							$cash = number_format($cash,2);
							$amount_lbl .= "Cash: $cash ";
							$bank_lbl .= " ";
							$cheque_number_lbl .= " ";
							$date_lbl .= " ";
							$payment_method_arr[] = [
							'lbl' => "$cash",
							'bank' => "<span class='span-block'>&nbsp;</span>",
							'check_num' => "",
							'date' => "<span class='span-block'>&nbsp;</span>"
							];
					}


				}
				/*if(isset($arr_payments['mem_credit'])){
					$mem_credit = $arr_payments['mem_credit']['amount']-$arr_payments['mem_credit']['amount_paid'];
					$mem_credit = number_format($mem_credit,2);
					$amount_lbl .= "<span class='span-block'>Credit: $mem_credit</span>";
					$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
					$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
					$date_lbl .= "<span class='span-block'>&nbsp;</span>";
				}*/
				if(isset($arr_payments['credit'])){


					if($type && !in_array(2,$type)){

					} else {
						foreach($arr_payments['credit'] as $arr_cheque){
							$cur_total += $arr_cheque['amount'];
							$total_cc += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "CC:  $cheque";
							$bank_lbl .= "$arr_cheque[bank] ";
							$cheque_number_lbl .= "$arr_cheque[ref_number] ";
							$date_lbl .= "$arr_cheque[date] ";
							$totalCollectedAmount +=  $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];
					}

				}
				if(isset($arr_payments['bt'])){

					if($type && !in_array(4,$type)){

					} else {
						foreach($arr_payments['bt'] as $arr_cheque){
							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "BT: $cheque";
							$bank_lbl .= "$arr_cheque[bank]";
							$cheque_number_lbl .= "$arr_cheque[ref_number]";
							$date_lbl .= "$arr_cheque[date]";
							$totalCollectedAmount +=  $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];
					}

				}

				if(isset($arr_payments['cheque'])){

					if($type && !in_array(3,$type)){

					} else {
						foreach($arr_payments['cheque'] as $arr_cheque){
							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "Cheque: $cheque ";
							$bank_lbl .= "$arr_cheque[bank] ";
							$cheque_number_lbl .= "$arr_cheque[ref_number] ";
							$date_lbl .= "$arr_cheque[date] ";
							$totalCollectedAmount +=  $arr_cheque['amount'];
						}
						$payment_method_arr[] = [
						'lbl' => "$cheque",
						'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
						'check_num' => "$arr_cheque[ref_number]",
						'date' => "<span class='span-block'>$arr_cheque[date]</span>"
						];
					}

				}
				if(isset($payment->terms)){
					$terms = $payment->terms;
				}
				if(!$amount_lbl) continue;

				$deduction = number_format($cc_perc * $total_cc,2,".","");
				if(isset($arr_payments['deduction'])){
					foreach($arr_payments['deduction'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;
					}
				}
				if(isset($arr_payments['consumable'])){
					foreach($arr_payments['consumable'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;
					}
				}
				if(isset($arr_payments['mem_credit'])){
					$deduct = $arr_payments['mem_credit']['amount']  - $arr_payments['mem_credit']['amount_paid'] ;
					$deduction += $deduct;
				}

				$totalReceiptAmount += $payment->stotal;
				$totalDeduction += (float) number_format($deduction,2,".","");
				$dr_pr_label = "";
				$invoice_label = "";
				if($payment->dr){
					$dr_pr_label = $payment->dr;
				} else if($payment->ir){
					$dr_pr_label = $payment->ir;
				} else {
					$dr_pr_label ='N/A';
				}
				if($payment->invoice){
					$invoice_label = $payment->invoice;
				} else {
					$invoice_label = "N/A";
				}

				$arraydata[] = array($date_sold,$dr_pr_label,$invoice_label,$payment->lastname,$total,$deduction,$amount_lbl,$bank_lbl,$cheque_number_lbl,$date_lbl,$terms);
				if(count($payment_method_arr) > 1){
					foreach($payment_method_arr as $pma){
						$arraydata[] = array('','','','','','',$pma['lbl'],$pma['bank'],$pma['check_num'],$pma['date'],'');

					}
				}
			}
			}



			$header = ['Delivery date','Receipt', 'Invoice','Client','Receipt Amt','Deduction','Check/Cash Amount','Bank','Check No.','Check date.','terms'];
			$widths = array(20, 15,15,55,15,22,35,30,30,35,10);
			$data = $pdf->LoadData($arraydata);

			$pdf->ImprovedTable($header,$widths,$data);
			//$total_sold,$total_paid,$total_per_member
		$pdf->Ln(7);
		$pdf->Cell(40,0,'Prepared by:  ______________________ ',0,0,'L',false);
		$pdf->Cell(0,0,'',0,0,'L',false);
		$pdf->Ln(7);

		$pdf->Cell(40,0,'Received by: ______________________ ',0,0,'L',false);
		$pdf->Cell(0,0,'',0,0,'L',false);
		$pdf->Ln(5);
		$pdf->Output();
	}
function printCollectionReportEmpty(){

		$date1 = Input::get('dt1');
		$date2 = Input::get('dt2');
		$salestype = Input::get('salestype');
		$terminal_id = Input::get('terminal_id');
		$user_id = Input::get('user_id');
		$type = Input::get('type');
		$cr_num = Input::get('cr_num');
		$paid_by = Input::get('paid_by');
		$from_service = Input::get('from_service');
		$show_with_cr = Input::get('show_with_cr');

		$cr_include_dr = Input::get('cr_include_dr');
		$cr_include_ir = Input::get('cr_include_ir');

		$diff_from_now = time() - strtotime($date1);
		$one_year = 31536000;

		$allow_old = ($diff_from_now > $one_year) ? true : false;

		$payment = new Payment();
		$arr_records = [];
		if(!$date1){
			if(!$cr_num){
			$date1 = date('m/d/Y');
			$date2 = strtotime($date1 . " 1 day -1 sec");
			$date1 = strtotime($date1);
			}

		} else {

				$date1 = strtotime($date1);
				$date2 = strtotime($date2 . " 1 day -1 sec");


		}

		if($cr_num){
			$cr_log = new Cr_log();
			$cr_list = $cr_log->getByCR($cr_num);
			if(false){ //$cr_list
				$with_log = true;
				$totalReceiptAmountLog = 0;
				$totalDeductionLog=0;
				$totalCollectedAmountLog=0;
				foreach($cr_list as $cr){
					$arr_rows=[];
					$arr_rows['res'] = array($cr->delivery_date,$cr->delivery_receipt,$cr->sales_invoice,$cr->client_name,$cr->receipt_amount,$cr->deduction,$cr->paid_amount,$cr->bank_name,$cr->check_no,$cr->check_date,$cr->terms);
					$arr_rows['type'] = 0;
					$arr_rows['dr'] = $cr->delivery_receipt;
					$arr_rows['check_num'] = $cr->check_no;
					$arr_records[] = $arr_rows;
					$totalReceiptAmountLog+= str_replace(",","",$cr->receipt_amount);
					$totalDeductionLog+= str_replace(",","",$cr->deduction);
					$totalCollectedAmountLog+= str_replace(",","",$cr->paid_amount);
				}

				$bydr = false;
				if(count($type) == 1 && $type[0] == 1){
					$bydr = true;
				}
				usort($arr_records, function($a, $b) use($bydr)
				{
					if($bydr){
						return $a['dr'] > $b['dr'];
					} else {
						 $rdiff = $a['type'] - $b['type'];
					    if ($rdiff) return $rdiff;
					      return strcmp($a['check_num'], $b['check_num']);

					}

				   // return strcmp($a['check_num'], $b['check_num']);
				});

				foreach($arr_records as $arr_record){
					$arraydata[] = $arr_record['res'];
				}
				echo json_encode(['result' =>$arraydata,'total_collected' => $totalCollectedAmountLog,'total_receipt' => $totalReceiptAmountLog,'total_deduction' => $totalDeductionLog]);
			}  // no log

		} // no CR
		if(!$with_log){


		$payments = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num,$from_service,$paid_by,$show_with_cr,'','');

		if($cr_include_dr || $cr_include_ir){
			$payments2 = $payment->getAllPayment($date1,$date2,$salestype,$terminal_id,$user_id,$cr_num,$from_service,$paid_by,$show_with_cr,$cr_include_dr,$cr_include_ir);
		}

		if($payments2 && is_array($payments2)){
			$payments = array_merge($payments,$payments2);

		}

		if($payments){
			$user= new User();
			$comp = new Company($user->data()->company_id);
			$company_name = $comp->data()->name;
			$address_company =$comp->data()->address;
			$addtl_info = $comp->data()->description;

			$totalCollectedAmount = 0;
			$totalReceiptAmount = 0;
			$totalDeduction = 0;
			$arraydata=[];

			$all_payment_ids = "";
			foreach($payments as $pm){
				$all_payment_ids .= $pm->id .",";
			}
			$all_payment_ids = rtrim($all_payment_ids,",");
			$all_payments_list = getPaymentsOrderIds($all_payment_ids);

			foreach($payments as $payment){

				//$payment_list = getPaymentsOrder($payment->id);
				//$payment_list
				//$payment_list
				$arr_payments = isset($all_payments_list[$payment->id]) ? $all_payments_list[$payment->id] : [];
				$total = $payment->stotal;
				$deduction = 0;
				$cash = 0;
				$amount_lbl = "";
				$bank_lbl = "";
				$cheque_number_lbl = "";
				$date_lbl = "";
				$terms = 0;
				$cur_total = 0;
				$total_cc = 0;
				$cc_perc = 0.035;
				$payment_method_arr = [];
				if($payment->is_scheduled){
					$date_sold = date('n/d',$payment->is_scheduled);
				} else {

					$date_sold = date('n/d',$payment->sold_date);

				}

				$lbl_from_service="";
				if($payment->from_service){
					$lbl_from_service ="SVC";
				}
				if(isset($payment->terms)){
					$terms = $payment->terms;
				}
				if(isset($arr_payments['cash'])){

					if($type && !in_array(1,$type)){

					} else {
						/*$cash = 0;
						foreach($arr_payments['cash'] as $arr_cash){
							$cash += $arr_cash['amount'];
							$cur_total += $arr_cash['amount'];
						}*/
							$cash = 0;
							foreach($arr_payments['cash'] as $arr_cash){
									$createdAt = $arr_cash['created'];
									if(($createdAt < $date1 || $createdAt > $date2 ) && !$cr_num && !$allow_old) continue;
									$cash += $arr_cash['amount'];
									$cur_total += $arr_cash['amount'];
							}

						if($cash){
							$cash = number_format($cash,2);
							$amount_lbl .= "Cash: $cash ";
							$bank_lbl .= " ";
							$cheque_number_lbl .= " ";
							$date_lbl .= " ";
							$payment_method_arr[] = [
							'type' => 1,
							'lbl' => "$cash",
							'bank' => "<span class='span-block'>&nbsp;</span>",
							'check_num' => "",
							'date' => "<span class='span-block'>&nbsp;</span>"
							];
						}

					}

				}
				/*if(isset($arr_payments['mem_credit'])){
					$mem_credit = $arr_payments['mem_credit']['amount']-$arr_payments['mem_credit']['amount_paid'];
					$mem_credit = number_format($mem_credit,2);
					$amount_lbl .= "<span class='span-block'>Credit: $mem_credit</span>";
					$bank_lbl .= "<span class='span-block'>&nbsp;</span>";
					$cheque_number_lbl .= "<span class='span-block'>&nbsp;</span>";
					$date_lbl .= "<span class='span-block'>&nbsp;</span>";
				}*/
				if(isset($arr_payments['credit'])){

					if($type && !in_array(2,$type)){

					} else {
						foreach($arr_payments['credit'] as $arr_cheque){
							$createdAt = $arr_cheque['created'];
							if($createdAt < $date1 || $createdAt > $date2 && !$allow_old) continue;

							$cur_total += $arr_cheque['amount'];
							$total_cc += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "CC:  $cheque";
							$bank_lbl .= "$arr_cheque[bank] ";
							$cheque_number_lbl .= "$arr_cheque[ref_number] ";
							$date_lbl .= "$arr_cheque[date] ";

							$payment_method_arr[] = [
							'type' => 2,
							'lbl' => "$cheque",
							'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
							'check_num' => "$arr_cheque[approval_code]",
							'date' => "<span class='span-block'>$arr_cheque[date]</span>"
							];
							$terms = $arr_cheque['trace_number'];
						}

					}

				}

				if(isset($arr_payments['bt'])){
					if($type && !in_array(4,$type)){

					} else {
						foreach($arr_payments['bt'] as $arr_cheque){

							$createdAt = $arr_cheque['created'];
							if($createdAt < $date1 || $createdAt > $date2 && !$allow_old) continue;

							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "BT: $cheque";
							$bank_lbl .= "$arr_cheque[bank]";
							$cheque_number_lbl .= "$arr_cheque[ref_number]";
							$date_lbl .= "$arr_cheque[date]";

							$payment_method_arr[] = [
							'type' => 4,
							'lbl' => "$cheque",
							'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
							'check_num' => "$arr_cheque[ref_number]",
							'date' => "<span class='span-block'>$arr_cheque[date]</span>"
							];
						}
					}

				}

				if(isset($arr_payments['cheque'])){

					if($type && !in_array(3,$type)){

					} else {
						foreach($arr_payments['cheque'] as $arr_cheque){
						$createdAt = $arr_cheque['created'];
						if(($createdAt < $date1 || $createdAt > $date2 ) && !$allow_old) continue;

							$cur_total += $arr_cheque['amount'];
							$cheque = number_format($arr_cheque['amount'],2);
							$amount_lbl .= "Cheque: $cheque ";
							$bank_lbl .= "$arr_cheque[bank] ";
							$cheque_number_lbl .= "$arr_cheque[ref_number] ";
							$date_lbl .= "$arr_cheque[date] ";

							$payment_method_arr[] = [
							'type' => 3,
							'lbl' => "$cheque",
							'bank' => "<span class='span-block'>$arr_cheque[bank]</span>",
							'check_num' => "$arr_cheque[ref_number]",
							'date' => "<span class='span-block'>$arr_cheque[date]</span>"
							];
						}

					}
				}


				if(!$amount_lbl) continue;
				$totalCollectedAmount +=$cur_total;
				//$deduction = number_format($cc_perc * $total_cc,2,".","");
				$arr_deductions = [];
				if(isset($arr_payments['deduction'])){
					foreach($arr_payments['deduction'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;

						$arr_deductions[] = $deduct;
					}
				}
				if(isset($arr_payments['consumable'])){

					foreach($arr_payments['consumable'] as $ind_deduction){
						$deduct = $ind_deduction['amount'];
						$deduction += $deduct;

						$arr_deductions[] = $deduct;
					}

				}
				if(isset($arr_payments['mem_credit'])){
					$deduct = $arr_payments['mem_credit']['amount']  - $arr_payments['mem_credit']['amount_paid'] ;
					$deduction += $deduct;
					$arr_deductions[] = $deduct;
				}

				$dr_pr_label = "";
				$invoice_label = "";
				$categ_label ='';
				if($_SERVER['HTTP_HOST'] == 'vitalite.apollosystems.com.ph' || $_SERVER['HTTP_HOST'] == 'localhost:81'){
					$arr_categ = [86,87,88,89,11];
					if(in_array($payment->category_id,$arr_categ)){
						$categ_label = "Gal.";
					}
				}
				if($payment->dr){
					$dr_pr_label = $payment->dr;
				} else if($payment->ir){
					$dr_pr_label = $payment->ir;
				} else {
					$dr_pr_label ='N/A';
				}
				if($payment->invoice){
					$invoice_label = $payment->invoice;
				} else {
					$invoice_label = "N/A";
				}
				$totalReceiptAmount += $payment->stotal;
				$totalDeduction += (float) number_format($deduction,2,".","");


				$first_deduction = '';
				if(count($payment_method_arr) == 1){
					if(count($arr_deductions)){
					$first_deduction = 0;
						foreach($arr_deductions as $adeduct){
						$first_deduction += $adeduct;
						}
					}

				} else {
					$first_deduction = isset($arr_deductions[0]) ? $arr_deductions[0] :  '';
				}
				if($payment_method_arr){
					$arr_rows = [];
					$arr_rows['res'] = array($date_sold,$dr_pr_label,$invoice_label,$payment->lastname,$total,$first_deduction. "|" . $categ_label,$payment_method_arr[0]['lbl'],$payment_method_arr[0]['bank'],$payment_method_arr[0]['check_num'],$payment_method_arr[0]['date'],$terms);
					$arr_rows['type'] = $payment_method_arr[0]['type'];
					$arr_rows['dr'] = $dr_pr_label;
					$arr_rows['check_num'] = $payment_method_arr[0]['check_num'];

					$arr_records[] = $arr_rows;
					if(count($payment_method_arr) > 1){
						$first = true;
						$ctr = 0;
						foreach($payment_method_arr as $pma){
							if($first) {
								$first = false;
								continue;
							}

							$arr_deduct = isset($arr_deductions[$ctr]) ? $arr_deductions[$ctr] : '';

							$arr_rows=[];
							$arr_rows['res'] = array($date_sold,$dr_pr_label,$invoice_label,$payment->lastname,'',$arr_deduct,$pma['lbl'],$pma['bank'],$pma['check_num'],$pma['date'],'');
							$arr_rows['type'] = $pma['type'];
							$arr_rows['dr'] = $dr_pr_label;
							$arr_rows['check_num'] = $pma['check_num'];

							$arr_records[] = $arr_rows;
							$ctr++;
						}
					}
				}

				}
					$bydr = false;
					if(count($type) == 1 && $type[0] == 1){
						$bydr = true;
					}
					usort($arr_records, function($a, $b) use($bydr)
					{
						if($bydr){
							return $a['dr'] > $b['dr'];
						} else {
							 $rdiff = $a['type'] - $b['type'];
						    if ($rdiff) return $rdiff;
						      return strcmp($a['check_num'], $b['check_num']);
						}


					   // return strcmp($a['check_num'], $b['check_num']);
					});

				foreach($arr_records as $arr_record){
					$arraydata[] = $arr_record['res'];
				}
			}
		echo json_encode(['result' =>$arraydata,'total_collected' => $totalCollectedAmount,'total_receipt' => $totalReceiptAmount,'total_deduction' => $totalDeduction]);
		} // no log
	}
	function saveLayout(){

		$layout = Input::get('layout');
		$type = Input::get('type');
		$cls = new Barcode();
		$user = new User();

		$cls->savePrintFormats($type,$layout,$user->data()->company_id);

		echo "Saved successfully.";

	}

function crEmpty(){

		$items= json_decode(Input::get('items'));

		$arr_records= [];
		$type = 1;
		if($items){

			foreach($items as $item){
			$item->delivery_receipt =  trim(str_replace('SVC','',$item->delivery_receipt));
			$item->sales_invoice =  trim(str_replace('SVC','',$item->sales_invoice));

			$item->receipt_amount = str_replace(',','',$item->receipt_amount);
			$item->deduction = str_replace(',','',$item->deduction);
			$item->paid_amount = str_replace(',','',$item->paid_amount);
			$temp_ex = explode('/',$item->delivery_date);
			$item->delivery_date = $temp_ex[0] . "/" . $temp_ex[1];
			$item->ar_number = ($item->ar_number) ? $item->ar_number : '';
			if($item->check_date){
				$temp_ex2 = explode('/',$item->check_date);
				if(count($temp_ex2)==2){
					$item->check_date .= "/" . $temp_ex[2];
				}
			}

			if($_SERVER['HTTP_HOST'] == 'cebuhiq.apollosystems.com.ph' || $_SERVER['HTTP_HOST'] == 'dev.apollo.ph:81'){

				$type = 2;
				 $ctr_number = "";
				 if ( $item->sales_invoice ) {
				    $ctr_number = $item->sales_invoice;
				 } else if ( $item->delivery_receipt ) {
				    $ctr_number = $item->delivery_receipt;
				 }

				$arr_records[] = array(
					$item->ar_number,
					$item->client_name,
					$item->delivery_date,
					$ctr_number,
					$item->receipt_amount,
					$item->deduction,
					$item->paid_amount,
					$item->bank_name,
					$item->check_no,
					$item->check_date,
					$item->terms,

				);

			} else {

				if($item->terms > 300) $item->terms = 0;
					$item->terms ='';
					$arr_records[] = array(
						$item->delivery_date,
						$item->delivery_receipt,
						$item->sales_invoice,
						$item->client_name,
						$item->receipt_amount,
						$item->deduction,
						$item->paid_amount,
						$item->bank_name,
						$item->check_no,
						$item->check_date,
						$item->terms
					);
				}

			}
		}
		echo json_encode(['result' =>$arr_records,'type' => $type]);
	}

	function arSummary(){
		$member_credit = new Member_credit();
		$branch_id = Input::get('branch_id');
		if($branch_id){
			$result = $member_credit->getByBranch($branch_id);
			if($result){
				echo "<table class='table table-bordered' id='tblForApproval'>";
				echo "<tr><th>Type</th><th>Total Amount</th></tr>";
				$total_all = 0;
				foreach($result as $res){
					$type = $res->sales_type_name ?  $res->sales_type_name  : 'No Type';
					$total_all += $res->total_credit;
					$total = number_format($res->total_credit,2);
					echo "<tr><td>$type</td><td><strong>$total</strong></td></tr>";
				}
				echo "<tr><th style='border-top:1px solid #ccc;'>Total</th><th style='border-top:1px solid #ccc;'>".number_format($total_all,2)."</th></tr>";
				echo "</table>";

			}
		} else {
			echo "Please choose branch first.";
		}
	}

	function arBYSalesman(){
		$member_credit = new Member_credit();
		$agent_id = Input::get('agent_id');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');
		$date_type = Input::get('date_type');
		$branch_id = Input::get('branch_id');

		$is_dl = Input::get('dl');
		$border ="";
		if($is_dl == 1){
				$filename = "ar-salesman-" . date('m-d-Y-H-i-s-A') . ".xls";
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-Type: application/vnd.ms-excel");
				$border = "border='1'";
		}

		if($agent_id){
			$result = $member_credit->getByAgent($agent_id,$branch_id,$dt_from,$dt_to,$date_type);
			$arr=[];
			if($result){
				foreach($result as $res){
					$type = $res->sales_type_name ?  $res->sales_type_name  : 'No Type';
					$arr[$type][] = $res;
				}
				echo "<table $border class='table table-bordered' id='tblForApproval'>";
						$colspan = 8;

					foreach($arr as $st => $data){
						echo "<tr><th  class='text-danger' style='border-top:1px solid #ccc;' colspan=$colspan>$st</th></tr>";
						echo "<tr>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "<th style='border-top:1px solid #ccc;'>Inv Date</th>";
						echo "<th style='border-top:1px solid #ccc;'>Type</th>";
						echo "<th style='border-top:1px solid #ccc;'>Inv/Dr</th>";
						echo "<th style='border-top:1px solid #ccc;'>Dr Amount</th>";
						echo "<th style='border-top:1px solid #ccc;'>Collected</th>";
						echo "<th style='border-top:1px solid #ccc;'>Balance</th>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "</tr>";
						$total_amount = 0;
						$total_paid = 0;
						$total_amount_member = 0;
						$total_paid_member = 0;
						$last = 0;
						$first = true;
						foreach($data as $d){
							$bal  =$d->amount - $d->amount_paid;
							$total_amount += $d->amount ;
							$total_paid += $d->amount_paid ;
							if($first){
								$d->credit_limit = ($d->credit_limit) ?: 'NA';
								$d->terms = ($d->terms) ?: 'NA';
								$display_name = "Client: <strong>". $d->member_name . "</strong> Credit Limit: <strong>" . $d->credit_limit . "</strong> Terms: <strong>"  . $d->terms. "</strong>";
							}
							if($date_type == 1){
								$dt = date('m/d/Y', $d->is_scheduled);
							} else {
								$dt = date('m/d/Y', $d->sold_date);
							}
							if($last != $d->member_name && !$first){
									$display_name =$d->member_name;
									echo "<tr>";
									echo "<td  class='text-danger'>Sub Total</td>";
									echo "<td></td>";
									echo "<td><strong>" .number_format($total_amount_member,2)."</strong></td>";
									echo "<td><strong>" .number_format($total_paid_member,2)."</strong></td>";
									echo "<td><strong>" .number_format($total_amount_member-$total_paid_member,2)."</strong></td>";
									echo "<td></td>";
									echo "</tr>";
									$total_amount_member = 0;
								$total_paid_member = 0;
							}
							$last = $d->member_name;
							$total_amount_member += $d->amount ;
							$total_paid_member += $d->amount_paid ;
							$first = false;
							$dr_num = "";
							$invoice_num = "";
							$pr_num = "";
							if($d->dr){
								$dr_num = "DR $d->dr";
							}
							if($d->invoice){
								$invoice_num = "Inv $d->invoice";
							}
							if($d->ir){
								$pr_num = "PR $d->ir";
							}
							if($display_name){
								echo "<tr>";
								echo "<td colspan='8'>$display_name</td>";

								echo "</tr>";
							}
							echo "<tr>";
							echo "<td></td>";
							echo "<td>$dt</td>";
							echo "<td>".$st."</td>";
							echo "<td>$invoice_num $dr_num $pr_num</td>";
							echo "<td>" .number_format($d->amount,2)."</td>";
							echo "<td>" .number_format($d->amount_paid,2)."</td>";
							echo "<td>" .number_format($bal,2)."</td>";
							echo "<td></td>";
							echo "</tr>";
							$display_name ='';
						}
						if($last){

									echo "<tr>";
									echo "<td class='text-danger'>Sub Total</td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "<td><strong>" .number_format($total_amount_member,2)."</strong></td>";
									echo "<td><strong>" .number_format($total_paid_member,2)."</strong></td>";
									echo "<td><strong>" .number_format($total_amount_member-$total_paid_member,2)."</strong></td>";
									echo "<td></td>";
									echo "</tr>";

							}

						echo "<tr>";
						echo "<th style='border-top:1px solid #ccc;' class='text-success'>Total $st</th>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "<th style='border-top:1px solid #ccc;'>".number_format($total_amount,2)."</th>";
						echo "<th style='border-top:1px solid #ccc;'>".number_format($total_paid,2)."</th>";
						echo "<th style='border-top:1px solid #ccc;'>".number_format($total_amount-$total_paid,2)."</th>";
						echo "<th style='border-top:1px solid #ccc;'></th>";
						echo "</tr>";
					}
				echo "</table>";

			} else {
			echo "No record.";
			}
		} else {
			echo "Please choose agent first.";
		}
}


function updatePaymentMethod(){
	$method = Input::get('method');
	 $cr_log_id = Input::get('cr_log_id');
	$cr_log = new Cr_log();

	if($method == 'credit'){
		$credit = new Credit();
		$id = Input::get('id');
		$card_number = Input::get('card_number');
		$bank_name = Input::get('bank_name');
		$card_type = Input::get('card_type');
		$approval_code = Input::get('approval_code');
		$trace_number = Input::get('trace_number');
		$date = Input::get('date');

		$credit->update(['card_number' => $card_number,'bank_name' => $bank_name,'card_type' => $card_type,'approval_code' => $approval_code,'trace_number' => $trace_number,'date' => strtotime($date)],$id);

		$date = date('n/j/y',strtotime($date));

		$cr_log->update(
			[
			'check_no' => $approval_code,
			'bank_name' =>$bank_name,
			'check_date' =>$date,
			], $cr_log_id
		);

		echo "Credit info was updated successfully.";

	} else if( $method == 'bt'){
		$bt = new Bank_transfer();
		$id = Input::get('id');
		$bankfrom_name = Input::get('bankfrom_name');
		$bankfrom_account_number = Input::get('bankfrom_account_number');
		$date = Input::get('date');

		$bt->update(['bankfrom_name' => $bankfrom_name,'bankfrom_account_number' => $bankfrom_account_number,'date' => strtotime($date)],$id);

		$date = date('n/j/y',strtotime($date));

		$cr_log->update(
			[
			    'check_no' => $bankfrom_account_number,
				'bank_name' =>$bankfrom_name,
				'check_date' =>$date,
			], $cr_log_id
		);


		echo "Bank Transfer was  updated successfully.";

	} else if( $method == 'cheque'){
		$cheque = new Cheque();
		//id:id,check_number:check_number,bank:bank,maturity_date:maturity_date
		$id = Input::get('id');
		$check_number = Input::get('check_number');
		$bank = Input::get('bank');
		$maturity_date = Input::get('maturity_date');
		$cheque->update(['check_number' => $check_number,'bank' => $bank,'	payment_date' => strtotime($maturity_date)],$id);

		$maturity_date = date('n/j/y',strtotime($maturity_date));

		$cr_log->update(
			[
			'check_no' => $check_number,
			'bank_name' =>$bank,
			'check_date' =>$maturity_date,
			], $cr_log_id
		);

		echo "Cheque was updated successfully.";
	}
}


	function finalizeCRAgent(){

		$cr_number = Input::get('cr_number');

		$cr_log = new Cr_log();

		$cr_log->approveCRLog($cr_number);

		echo "Request updated successfully.";

	}

	function getCRContent(){

		$cr_number  = Input::get('cr_number');

		$cr = new Cr_log();

		$list = $cr->byCR($cr_number);
		if($list)
			echo json_encode($list);
		else
			echo json_encode([]);
	}

	function deleteCRContent(){
		$id = Input::get('id');
		if($id && is_numeric($id)){

			$cr = new Cr_log($id);

			$payment_info = $cr->data()->payment_info;

			$payment_info = json_decode($payment_info);

			if($payment_info->payment_id){

				$cr_log_ids = new Cr_log_ids();
				$cr_log_ids->deleteCrDetails($payment_info->payment_id,$payment_info->id);

				//delete cr log
				$cr_log = new Cr_log();
				$cr_log->deleteDetails($id);

				$payment = new Payment();
				$payment->update(
					[ 'cr_number' => '', 'cr_date' => 0 ] , $payment_info->payment_id
				);

				echo "CR Details deleted successfully.";
			}

		}
	}

	function agentCRList(){

		$user = new User();
		$cid = $user->data()->company_id;
		$args = Input::get('page');
		$page = new Pagination(new Payment());
		$page->setCompanyId($cid);
		$page->setPageNum($args);
		$page->paginate();

	}