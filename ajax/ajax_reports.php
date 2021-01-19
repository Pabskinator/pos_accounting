<?php

	include 'ajax_connection.php';

	$functionName = Input::get("functionName");

	$functionName();


	function item(){
		$sales = new Sales();
		$form = json_decode(Input::get('form'));
		$type = $form->type;
		$branch_id = $form->branch_id;
		$sort_type = $form->sort_type;

		$date_type = $form->date_type;

		$date_from_last_year = strtotime($form->date_from . "-1 year");
		$date_to_last_year= strtotime($form->date_to . "-1 year -1 sec");

		$list = $sales->reportItem($type,$branch_id,$form->date_from,$form->date_to,$date_from_last_year,$date_to_last_year,$form->limit_by,$form->item_type,$sort_type,0,$date_type);

		$arr = [];
		$current_year = 0;
		$prev_year = 0;

		if($form->date_from){
			$current_year = date('Y',strtotime($form->date_from));
			$prev_year = $current_year - 1;
		}

		$total_current = 0;
		$qty_current = 0;
		$total_prev = 0;
		$qty_prev = 0;
		$total_cost = 0;
		if($list){

			foreach($list as $i){

				if($type == 1){
					$name = $i->item_code . "<small class='span-block text-danger'>".$i->description."</small><small class='span-block text-danger'>".$i->category_name."</small>";

				} else if ($type == 2){
					$name = $i->category_name;
				}
				$last_year_saletotal = $i->lysalestotal;
				$last_year_qtytotal = $i->lyqtytotal;

				$total_current += $i->saletotal;
				$qty_current += $i->qtytotal;

				$total_prev += $last_year_saletotal;
				$qty_prev += $last_year_qtytotal;
				$total_cost += ($i->product_cost * $i->qtytotal);



				$arr[] = [
					'name' => $name,
					'cost' =>  number_format(($i->product_cost * $i->qtytotal) ,2),
					'qty' =>  formatQuantity($i->qtytotal),
					'total' => number_format($i->saletotal,2),
					'last_year_qty' =>  $last_year_qtytotal,
					'last_year_total' => number_format($last_year_saletotal,2),
				];
			}
		}
		$totals = [
			'total_current' => number_format($total_current,2),
			'qty_current' => formatQuantity($qty_current),
			'cost_current' => number_format($total_cost,2),
			'total_prev' =>  number_format($total_prev,2),
			'qty_prev' => number_format($qty_prev,2)
		];

		echo json_encode(
			[
				'results' => $arr,
				'current_year' => $current_year,
				'prev_year' => $prev_year,
				'totals' => $totals

			]
		);
	}

	function itemDownload(){

		$filename = "item-reports-" . date('m-d-Y-h-i-s') . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");

		header("Content-Type: application/vnd.ms-excel");

		$sales = new Sales();

		$form = json_decode(Input::get('form'));

		$type = $form->type;

		$branch_id = $form->branch_id;

		$sort_type = $form->sort_type;

		$date_type = $form->date_type;

		$date_from_last_year = strtotime($form->date_from . "-1 year");

		$date_to_last_year= strtotime($form->date_to . "-1 year -1 sec");

		$list = $sales->reportItem($type,$branch_id,$form->date_from,$form->date_to,$date_from_last_year,$date_to_last_year,$form->limit_by,$form->item_type,$sort_type,$dl=1,$date_type);

		$arr = [];

		$current_year = 0;

		$prev_year = 0;

		if($form->date_from){

			$current_year = date('Y',strtotime($form->date_from));

			$prev_year = $current_year - 1;

		}

		$total_current = 0;

		$qty_current = 0;

		$total_prev = 0;

		$qty_prev = 0;

		$total_cost = 0;

		if($list){

			foreach($list as $i){

				if($type == 1){

					$name = $i->item_code;
					$description = $i->description;

				} else if ($type == 2){

					$name = $i->category_name;
					$description = '';

				}

				$last_year_saletotal = $i->lysalestotal;
				$last_year_qtytotal = $i->lyqtytotal;

				$total_current += $i->saletotal;
				$qty_current += $i->qtytotal;

				$total_prev += $last_year_saletotal;
				$qty_prev += $last_year_qtytotal;
				$total_cost += ($i->product_cost * $i->qtytotal);

				$arr[] = [
					'name' => $name,
					'description' => $description,
					'category_name' => $i->category_name,
					'parent_name' => $i->parent_name,
					'cost' =>  ($i->product_cost * $i->qtytotal),
					'qty' =>  $i->qtytotal,
					'total' => $i->saletotal,
					'last_year_qty' =>  $last_year_qtytotal,
					'last_year_total' => $last_year_saletotal,
				];
			}
		}

		$totals = [
			'total_current' => number_format($total_current,2),
			'qty_current' => number_format($qty_current,2),
			'cost_current' => number_format($total_cost,2),
			'total_prev' =>  number_format($total_prev,2),
			'qty_prev' => number_format($qty_prev,2)
		];

		$bname = "None";
		if($branch_id){
			$branch = new Branch($branch_id);
			$bname = $branch->data()->name;
		}

		$fdate = "None";
		if($form->date_from && $form->date_to){
			$fdate = $form->date_from ."-".$form->date_to;
		}
		echo "<table border='1' style='text-align: left'>";
		echo "<tr><th colspan='6' style='text-align: left'>Item Report</th></tr>";
		echo "<tr><td colspan='6' style='text-align: left'>Branch: ".$bname."</td></tr>";
		echo "<tr><td colspan='6' style='text-align: left'>Date: ".$fdate ."</td></tr>";
		echo "</table>";
		echo "<table border=1>";
		echo "<tr><th>Category</th><th>Name</th><th></th><th>Qty</th><th>Total Cost</th><th>Total Sale</th></tr>";
		$last_categ = "";
		$first = true;
		$cur_total = 0;
		$cur_qty = 0;
		$cur_cost = 0;
		$cur_qty_parent = 0;
		$cur_total_parent = 0;
		$cur_cost_parent = 0;
		$last_parent = '';

		foreach($arr as $a){
			$categ = '';
			$parent = '';


			if($last_categ != $a['category_name']){
				$last_categ = $a['category_name'];
				$categ = $a['category_name'];
				if($a['parent_name']){
					$categ = $a['parent_name'] . "-" . $categ;
				}

				if(!$first){
					echo "<tr><th></th><th></th><th></th><th>$cur_qty</th><th>$cur_cost</th><th>$cur_total</th></tr>";
					$cur_total = 0;
					$cur_qty = 0;
					$cur_cost = 0;

				}

			}

			if($last_parent !=  $a['parent_name']){

				if(!$first){
					echo "<tr><th>$last_parent</th><th></th><th></th><th>$cur_qty_parent</th><th>$cur_cost_parent</th><th>$cur_total_parent</th></tr>";
					$cur_total_parent = 0;
					$cur_qty_parent = 0;
					$cur_cost_parent = 0;
				}
				$last_parent = $a['parent_name'];
				$parent = $a['parent_name'];
			}



			$cur_cost += str_replace(',','',$a['cost']);

			$cur_total += str_replace(',','',$a['total']);
			$cur_qty += str_replace(',','',$a['qty']);
			$cur_total_parent += str_replace(',','',$a['total']);
			$cur_cost_parent += str_replace(',','',$a['cost']);
			$cur_qty_parent += str_replace(',','',$a['qty']);

			echo "<tr>";
			echo "<td>";

			echo $categ;

			echo "</td>";
			echo "<td>$a[name]</td>";
			echo "<td>$a[description]</td>";
			echo "<td>$a[qty]</td>";
			echo "<td>$a[cost]</td>";
			echo "<td>$a[total]</td>";
			echo "</tr>";
			if($first){
				$first = false;
			}
		}

		echo "<tr><th></th><th></th><th></th><th>$cur_qty</th><th>$cur_cost</th><th>$cur_total</th></tr>";
		echo "<tr><th>$last_parent</th><th></th><th></th><th>$cur_qty_parent</th><th>$cur_cost_parent</th><th>$cur_total_parent</th></tr>";
		echo "<tr><th></th><th></th><th></th><th>$totals[qty_current]</th><th>$totals[cost_current]</th><th>$totals[total_current]</th></tr>";

		echo "</table>";
	}


	function member(){

		$form = json_decode(Input::get('form'));


		$date_from_last_year = strtotime($form->date_from . "-1 year");
		$date_to_last_year= strtotime($form->date_to . "-1 year -1 sec");
		$user = new User();
		$dt1 = strtotime($form->date_from);
		$dt2 = strtotime($form->date_to . "1 day -1 sec");
		$type = $form->type;
		$date_type = $form->date_type;
		$arr = [];
		$total = 0;
		if($type == 1){

			$sales = new Sales();
			$data = $sales->topClientSales($user->data()->company_id,$dt1,$dt2,$form->limit_by,$form->branch_id,$form->sales_type_id,$date_type);

			if($data){
				foreach($data as $d){
					$total += $d->saletotal;
					$arr[] = [
						'member_name' => $d->member_name,
						'sales_type_name'=> $d->sales_type_name,
						'total' => number_format($d->saletotal,2)
					];
				}
			}

		} else if ($type == 2){
			$member_credit = new Member_credit();

			$data = $member_credit->topMemberCredit($user->data()->company_id,$dt1,$dt2,$form->limit_by);
			if($data){

				foreach($data as $d){
					$total += $d->credittotal;
					$arr[] = ['member_name' => $d->member_name,'sales_type_name'=> $d->sales_type_name,'total' => number_format($d->credittotal,2)];
				}
			}
		}

		echo json_encode(['results' => $arr,'total' => number_format($total,2)]);

	}
	function sorbyAlpha($a, $b)
	{
		return strcmp($a['sales_type_name'], $b['sales_type_name']);
	}


	function memberDownload(){

		$form = json_decode(Input::get('form'));

		$user = new User();
		$dt1 = strtotime($form->date_from);
		$dt2 = strtotime($form->date_to . "1 day -1 sec");
		$type = $form->type;
		$date_type = $form->date_type;
		$arr = [];
		$filename = "sales-type-total-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		if($type == 1){

			$sales = new Sales();
			$data = $sales->topClientSales($user->data()->company_id,$dt1,$dt2,$form->limit_by,$form->branch_id,$form->sales_type_id,$date_type);

			if($data){

				foreach($data as $d){
					$d->sales_type_name = ($d->sales_type_name) ? $d->sales_type_name : 'No type';
					$arr[] = [
						'member_name' => $d->member_name,
						'sales_type_name'=> $d->sales_type_name,
						'total' => number_format($d->saletotal,2)
					];
				}
			}

			usort($arr, "sorbyAlpha");

			echo "<table border=1 >";
			echo "<thead><tr><th>Type</th><th>Client</th><th>Total</th></tr></thead>";
			echo "<tbody>";

			$total = 0;
			$prev = '';
			$first = true;
			$grand_total = 0;
			foreach($arr as $a){

				if($prev != $a['sales_type_name'] && !$first){
					echo "<tr><td></td><td></td><td><strong>".number_format($total,2)."</strong></td></tr>";
					$total = 0;
				}
				$prev = $a['sales_type_name'];
				echo "<tr>";
				echo "<td>$a[sales_type_name]</td>";
				echo "<td>$a[member_name]</td>";
				echo "<td>$a[total]</td>";
				echo "</tr>";

				$total += (str_replace(',','',$a['total']));
				$grand_total +=  (str_replace(',','',$a['total']));
				$first = false;

			}
			echo "<tr><td></td><td></td><td><strong>".number_format($total,2)."</strong></td></tr>";
			echo "<tr><td></td><td></td><td><strong>".number_format($grand_total,2)."</strong></td></tr>";
			echo "</tbody>";
			echo "</table>";


		} else if ($type == 2){

			$member_credit = new Member_credit();

			$data = $member_credit->topMemberCredit($user->data()->company_id,$dt1,$dt2,$form->limit_by);

			if($data){

				foreach($data as $d){
					$d->sales_type_name = ($d->sales_type_name) ? $d->sales_type_name : 'No type';
					$arr[] = ['member_name' => $d->member_name,'sales_type_name'=> $d->sales_type_name,'total' => number_format($d->credittotal,2)];
				}

			}

			usort($arr, "sorbyAlpha");

			echo "<table border=1 >";
			echo "<thead><tr><th>Type</th><th>Client</th><th>Total</th></tr></thead>";
			echo "<tbody>";
			foreach($arr as $a){

				echo "<tr>";
				echo "<td>$a[sales_type_name]</td>";
				echo "<td>$a[member_name]</td>";
				echo "<td>$a[total]</td>";
				echo "</tr>";

			}
			echo "</tbody>";
			echo "</table>";

		}


	}

	function rawSummary(){
		$form = json_decode(Input::get('form'));


		$dt1 = strtotime($form->date_from);
		$dt2 = strtotime($form->date_to . "1 day -1 sec");

		if(!$form->date_from){
			$dt1 = strtotime(date('m/1/Y'));
			$dt2 = strtotime(date('m/1/Y') . "1 month -1 sec");
		}

		$composite_item = new Composite_item();
		$list = $composite_item->compositeReport($dt1,$dt2);
		$arr = [];
		if($list){
			foreach($list as $item){
				$item->r_qty = $item->qty * $item->out_qty;
				$item->raw_qty =number_format($item->r_qty,3);
				$item->qty = (number_format($item->qty,3));
				$item->out_qty = (number_format($item->out_qty,3));

				$arr[]= $item;
			}
			usort($arr, function($a, $b)  {

				return $a->r_qty < $b->r_qty;
			});
		}

		echo json_encode(['results' => $arr]);
	}

	function orderSummary(){
		$form = json_decode(Input::get('form'));

		$branch_id = $form->branch_id;
		$branch_id_except = $form->except;

		$user = new User();
		$dt1 = strtotime($form->date_from);
		$dt2 = strtotime($form->date_to . "1 day -1 sec");

		if(!$form->date_from){
			$dt1 = strtotime(date('m/1/Y'));
			$dt2 = strtotime(date('m/1/Y') . "1 month -1 sec");
		}

		$wh = new Wh_order();
		$data = $wh->getSummaryOrder($dt1,$dt2,$branch_id,$branch_id_except);

		$sales = new Sales();
		$data_sales = $sales->summaryByItem($dt1,$dt2,$branch_id,$branch_id_except);

		$inventory = new Inventory();
		$data_inv = $inventory->get_audit_record($user->data()->company_id,0,1000,$branch_id);


		$arr_orders = [];
		$arr_sales = [];
		$arr_inv= [];
		$arr_items = [];
		$arr = [];

		if($data){
			foreach($data as $d){
				if(!$d->totalquantity) continue;
				$arr_orders[$d->item_code] = ['total' => number_format($d->totalquantity,0,'','')];
				if(!in_array($d->item_code,$arr_items)) $arr_items[] = $d->item_code;
			}
		}
		if($data_sales){
			foreach($data_sales as $d){
				if(!$d->totalquantity) continue;
				$arr_sales[$d->item_code] = ['total' => number_format($d->totalquantity,0,'','')];
				if(!in_array($d->item_code,$arr_items)) $arr_items[] = $d->item_code;
			}
		}
		if($data_inv){
			foreach($data_inv as $d){
				if(!$d->qty) continue;
				$arr_inv[$d->item_code] = ['total' => number_format($d->qty,0,'','')];
				if(!in_array($d->item_code,$arr_items)) $arr_items[] = $d->item_code;
			}
		}


		foreach($arr_items as $item){
			$total_order = isset($arr_orders[$item]) ? $arr_orders[$item]['total'] : 0;
			$total_sales = isset($arr_sales[$item]) ? $arr_sales[$item]['total'] : 0;
			$total_inv = isset($arr_inv[$item]) ? $arr_inv[$item]['total'] : 0;

			$arr[]= ['item_code' => $item,'total_order' => $total_order, 'total_sales' => $total_sales,'total_inv' => $total_inv];

		}
		echo json_encode(['results' => $arr]);

	}


	function memberSummary(){

		$dt = Input::get('dt');
		$dt_from = Input::get('dt_from');
		$dt_to = Input::get('dt_to');

		if($dt){
			$dt = int($dt);
		} else if ($dt_from && $dt_to){
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to . "1 day -1 min");

		} else {
			$dt = date('Y');
		}

		$sales = new Sales();
		$user = new User();

		$data = $sales->memberSummary($user->data()->company_id,$dt,$dt_from,$dt_to);

		$results = [];
		$arr_mem = [];
		$arr_month = [];
		$arr_st = [];

		if($data){

			foreach($data as $d){

				$mem = $d->member_name;
				if(!in_array($mem,$arr_mem))
					$arr_mem[] = $mem;


				$arr_st[$mem] = $d->sales_type_name;

				$key = $d->m ."-".$d->y;

				if(!in_array($key,$arr_month)){
					$arr_month[] = $key;
				}
				$results[$mem][$key] = $d->totalamount;
			}

		}

		ksort($results);

		$monthly = [];
		$arr_mem_total  = [];
		foreach($arr_mem as $mem){
			$member_name  = $mem;

			$gtotal = 0;
			foreach($arr_month as $ekey){
				$total= isset($results[$member_name][$ekey]) ? $results[$member_name][$ekey] : 0;
				$monthly[$ekey] =  isset($monthly[$ekey]) ? $monthly[$ekey] : 0;
				$monthly[$ekey] += $total;
				$gtotal += $total;


			}
			$arr_mem_total[$member_name] = $gtotal;

		}

		$total_monthly= 0;
		$arr = [];
		foreach($arr_month as $ekey){
			$total_monthly  += $monthly[$ekey];
			$monthNum  = $ekey;
			//$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			//$monthName = $dateObj->format('M');
			$obj['y'] =  $monthNum;
			$obj['a'] = $monthly[$ekey];
			array_push($arr,$obj);
		}
		if($arr){
			echo json_encode(['keys' => $arr_month,'total' =>$arr,'results' => $results,'member_total' => $arr_mem_total,'st' => $arr_st]);
		} else {
			echo json_encode(array('error' => true));
		}
	}

	function memberSummaryByAgent(){

		$is_dl = Input::get('is_dl') ?  Input::get('is_dl') : 0;
		$border = "";
		$time_start = microtime(true);




		if($is_dl){
			$border = "border=1";
			$filename = "sales-type-summary-" . date('m-d-Y-h-i-s') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		}



		$sales = new Sales();
		$user = new User();
		$type = Input::get('sales_type_id');
		$data = $sales->memberSummaryByAgent($user->data()->company_id,$type);
		$results = [];
		$years = [];
		$last_sold_arr = [];

		if($data){
			foreach($data as $d){

				if(!in_array($d->y,$years)) $years[] = $d->y;
				if(!in_array($d->last_sold_date,$last_sold_arr)) $last_sold_arr[$d->member_name] = $d->last_sold_date;

				$results[$d->sales_type_name][$d->member_name][$d->y] = $d->totalamount;

			}
			$ret_arr = [];
			echo "<table class='table table-bordered' $border>";
			echo "<thead><tr>";
			echo "<th>Sales Type</th>";
			echo "<th>Client Name</th>";
			echo "<th>Last Transaction</th>";
			$arr_totals = [];
			foreach($years as $year){
				echo "<th class='text-right'>$year</th>";
				$arr_totals[$year] = 0;
			}
			echo "</tr></thead>";

			foreach($results as $type => $another_arr){
				$type = ($type) ? $type : 'NA';
				foreach($another_arr as $member_name => $last_arr){
					$last_transaction_date =  isset($last_sold_arr[$member_name])  ?   date('m/d/Y',$last_sold_arr[$member_name]) : 'NA';
					echo "<tr>";
					echo "<td style='border-top:1px solid #ccc;'>".$type."</td>";
					echo "<td  style='border-top:1px solid #ccc;'>".$member_name."</td>";
					echo "<td  style='border-top:1px solid #ccc;'>".$last_transaction_date."</td>";
					$member_name = ($member_name) ? $member_name : '';
					foreach($years as $year){
						$sales_total = isset($last_arr[$year]) ? $last_arr[$year] : 0;
						$ret_arr[] =[
							'sales_type_name' => $type,
							'member_name' => $member_name,
							'year' => $year,
							'sales_total' => $sales_total,
						];

						echo "<td class='text-right'  style='border-top:1px solid #ccc;'>". number_format($sales_total,2)."</td>";
						$arr_totals[$year] +=$sales_total;
					}
					echo "</tr>";


				}
			}
			echo "<tr><th  style='border-top:1px solid #ccc;'></th><th  style='border-top:1px solid #ccc;'></th><th  style='border-top:1px solid #ccc;'></th>";
			foreach($years as $year){
				echo "<th class='text-right'  style='border-top:1px solid #ccc;'>".number_format($arr_totals[$year],2)."</th>";

			}
			echo "</tr>";


		}

		$time_end = microtime(true);
		$execution_time = number_format($time_end - $time_start,4);
		if($is_dl){
			Log::addLog(
				$user->data()->id,
				$user->data()->company_id,
				"DOWNLOAD CLIENT SUMMARY REPORT - " . $execution_time,
				'excel_downloader.php'
			);
		}

	}

	function itemSummary(){

		$dt = Input::get('dt');
		$branch_id = Input::get('branch_id');
		$by_what = Input::get('by_what');
		$order_by = Input::get('order_by');

		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->itemSummary($user->data()->company_id,$dt,$by_what,$branch_id,$order_by);

		$results = [];
		$arr_item = [];
		$to_sort = [];
		if($data){
			foreach($data as $d){
				if(!in_array($d->item_code,$arr_item))
					$arr_item[] = $d->item_code;

				$results[$d->item_code][$d->m] = number_format($d->totalamount,2,".","");
				if(isset($to_sort[$d->item_code])){
					$to_sort[$d->item_code] += $d->totalamount;
				} else {
					$to_sort[$d->item_code] = $d->totalamount;
				}
				$categories[$d->item_code] = $d->category_name;



			}
		}
		if($order_by == 1){
			arsort($to_sort);
			ksort($results);
			$new_arr = [];
			foreach($to_sort as $it => $vl){
				for($i=1;$i<=12;$i++) {
					if(isset($results[$it][$i])){
						$new_arr[$it][$i] = $results[$it][$i];
					} else {
						$new_arr[$it][$i] = 0;
					}
				}
			}
			$results = $new_arr;
		} else if ($order_by == 2){

		}

		$results = array_slice($results,0,1000);
		$monthly = [];

		foreach($arr_item as $item_code){


			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$item_code][$i]) ? $results[$item_code][$i] : 0;
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
			echo json_encode(['total' =>$arr,'results' => $results,'categories'=> $categories]);
		} else {
			echo json_encode(array('error' => true));
		}
	}
	function downloadItemSummary(){

		$filename = "item-reports-" . date('m-d-Y-h-i-s') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$dt = Input::get('dt');
		$branch_id = Input::get('branch_id');
		$by_what = Input::get('by_what');
		$order_by = Input::get('order_by');

		$dt = ($dt) ? $dt : date('Y');
		$sales = new Sales();
		$user = new User();
		$data = $sales->itemSummary($user->data()->company_id,$dt,$by_what,$branch_id,$order_by);

		$results = [];
		$arr_item = [];
		$to_sort = [];
		if($data){
			foreach($data as $d){
				$d->description = str_replace('"','',$d->description);
				$d->description = str_replace("'",'',$d->description);
				if(!in_array($d->description,$arr_item))
					$arr_item[] = $d->description;

				$results[$d->description][$d->m] = $d->totalamount;
				if(isset($to_sort[$d->description])){
					$to_sort[$d->description] += $d->totalamount;
				} else {
					$to_sort[$d->description] = $d->totalamount;
				}
				$categories[$d->description] = $d->category_name;
			}
		}
		if($order_by == 1){
			arsort($to_sort);
			ksort($results);
			$new_arr = [];
			foreach($to_sort as $it => $vl){
				for($i=1;$i<=12;$i++) {
					if(isset($results[$it][$i])){
						$new_arr[$it][$i] = $results[$it][$i];
					} else {
						$new_arr[$it][$i] = 0;
					}
				}
			}
			$results = $new_arr;
		} else if ($order_by == 2){

		}

		$monthly = [];

		foreach($arr_item as $item_code){


			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$item_code][$i]) ? $results[$item_code][$i] : 0;
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
		echo "<table border=1>";
		?>
		<tr>
			<th>Item</th><th  class='text-right'>Jan</th><th  class='text-right'>Feb</th><th  class='text-right'>March</th><th  class='text-right'>April</th><th  class='text-right'>May</th><th  class='text-right'>June</th><th  class='text-right'>July</th><th  class='text-right'>August</th><th  class='text-right'>Sept</th><th  class='text-right'>Oct</th><th  class='text-right'>Nov</th><th  class='text-right'>Dec</th><th>Total</th>
		</tr>
		<?php
		foreach($results as $res => $det){
			echo "<tr>";
			echo "<td>$res</td>";
			$g_total = 0;
			for($i=1;$i<=12;$i++){
				$total = isset($det[$i]) ? $det[$i] :0;
				$g_total += $total;
				echo "<td>$total</td>";
			}
			echo "<td>$g_total</td>";
			echo "</tr>";
		}
		echo "</table>";

	}
	function rawSummaryYear(){

		$dt = Input::get('dt');
		$dt = ($dt) ? $dt : date('Y');
		$composite_item = new Composite_item();

		$data = $composite_item->summaryRaw($dt);

		$results = [];
		$arr_item = [];
		if($data){
			foreach($data as $d){
				if(!in_array($d->item_code,$arr_item))
					$arr_item[] = $d->item_code;
				if(isset($results[$d->item_code][$d->m])){
					$results[$d->item_code][$d->m] += ($d->out_qty * $d->qty);
				} else {
					$results[$d->item_code][$d->m] = ($d->out_qty * $d->qty);
				}

			}
		}
		ksort($results);


		$monthly = [];
		$item_total = [];
		foreach($arr_item as $item_code){


			$gtotal = 0;
			for($i=1;$i<=12;$i++){
				$total= isset($results[$item_code][$i]) ? $results[$item_code][$i] : 0;
				$monthly[$i] =  isset($monthly[$i]) ? $monthly[$i] : 0;
				$monthly[$i] += $total;
				$gtotal += $total;


			}
			$item_total[$item_code] = $gtotal;

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
		$obj['y'] =  '';
		$obj['a'] = $total_monthly;
		array_push($arr,$obj);
		if($arr){
			echo json_encode(['total' =>$arr,'results' => $results,'item_total' => $item_total]);
		} else {
			echo json_encode(array('error' => true));
		}
	}


	function getPriceMatrix(){
		$filename = "price-matrix-" . date('m-d-Y-H-i-s-A') . ".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		$prod = new Product();
		$user = new User();

		$branch= new Branch($user->data()->branch_id);
		$branch_id = $branch->data()->id;
		$list = $prod->priceMatrix($branch_id);
		$price_group = new Price_group();
		$price_groups = $price_group->getPG();
		$item_price_adj = new Item_price_adjustment();
		$price_group_adj = $item_price_adj->getAdjustmentPriceGroupAll();
		$arr_price_group = [];
		foreach($price_group_adj as $adj){
			$arr_price_group[$adj->price_group_id][$adj->item_id] = $adj->adjustment;
		}

		?>

		<table border=1>
			<thead>
			<tr>
				<th>Category</th>
				<th>Item</th>
				<th>Description</th>

				<th>Srp</th>
				<?php
					foreach($price_groups as $pg){
						echo "<th>$pg->name</th>";
					}
				?>
			</tr>
			</thead>
			<tbody>
				<?php


					foreach($list as $l){
						$orig = $l->price;
						$branch_adj = $l->adjustment;
						$branch_adj = ($branch_adj) ? $branch_adj : 0;
						$srp = $orig + $branch_adj;
						?>
						<tr>
							<td><?php echo $l->category_name; ?></td>
							<td><?php echo $l->item_code; ?></td>
							<td><?php echo $l->description; ?></td>
							<td><?php echo $srp; ?></td>
							<?php
								foreach($price_groups as $pg){
									$ind_price_adj = isset($arr_price_group[$pg->id][$l->item_id]) ? $arr_price_group[$pg->id][$l->item_id] : 0 ;
									$is_red = '';
									if($ind_price_adj) $is_red = "style='color:red;'";
									$ind_price_adj += $srp;

									echo "<td $is_red>$ind_price_adj</td>";
								}
							?>
						</tr>

						<?php
					}
				?>
			</tbody>
		</table>
		<?php

	}

