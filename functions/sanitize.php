<?php

function escape($string){
	return htmlentities($string,ENT_QUOTES,'UTF-8');

}
	/**
	 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
	 * @author Joost van Veen
	 * @version 1.0
	 */
	if (!function_exists('dump')) {
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

	}


	if (!function_exists('dump_exit')) {
		function dump_exit($var, $label = 'Dump', $echo = TRUE) {
			dump ($var, $label, $echo);
			exit;
		}
	}
	if (!function_exists('convertNumberToWord')) {
		function convertNumberToWord($num = false)
		{
			$num = str_replace(array(',', ' '), '' , trim($num));
			if(! $num) {
				return false;
			}
			$num = (int) $num;
			$words = array();
			$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
				'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
			);
			$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
			$list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
				'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
				'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
			);
			$num_length = strlen($num);
			$levels = (int) (($num_length + 2) / 3);
			$max_length = $levels * 3;
			$num = substr('00' . $num, -$max_length);
			$num_levels = str_split($num, 3);
			for ($i = 0; $i < count($num_levels); $i++) {
				$levels--;
				$hundreds = (int) ($num_levels[$i] / 100);
				$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
				$tens = (int) ($num_levels[$i] % 100);
				$singles = '';
				if ( $tens < 20 ) {
					$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
				} else {
					$tens = (int)($tens / 10);
					$tens = ' ' . $list2[$tens] . ' ';
					$singles = (int) ($num_levels[$i] % 10);
					$singles = ' ' . $list1[$singles] . ' ';
				}
				$words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
			} //end for loop
			$commas = count($words);
			if ($commas > 1) {
				$commas = $commas - 1;
			}
			return ucwords(implode(' ', $words));
		}
	}



	function getDays($date=''){
		if($date){
			$now = strtotime(date('m/d/Y',time()));

			$date = strtotime($date);
			$days = $date - $now;


			return floor((($days/60)/60)/24);
		}
	}
	function removeComma($val){
		return str_replace(',','',$val);
	}

	function is_decimal( $val )
	{
		return is_numeric( $val ) && floor( $val ) != $val;
	}
	function formatQuantity($v,$noComma = false){
		if(is_decimal($v)){
			if($noComma){
				return escape(number_format($v,3,'.',''));
			} else {
				return escape(number_format($v,3));
			}
		} else {
			if($noComma){
				return escape(number_format($v,0,'.',''));
			} else {
				return escape(number_format($v));
			}
		}
	}
	function getLandingPageOrder($user){
		if($user && is_object($user)){

		}
	}
	function capitalize($str){
		if($str){
			return ucwords(strtolower($str));
		}
		return '';
	}
	if(!function_exists('isInventoryValid')){
			function isInventoryValid($branch_id,$item_id,$qty){

			$valid = 0;
			if($branch_id && $item_id && $qty){
				$item_cls = new Product($item_id);
				$composite = new Composite_item();
				$is_composite = $composite->hasSpare($item_id);
				$inv = new Inventory();
				$stock = $inv->getAllQuantity($item_id,$branch_id);

				$whorder = new Wh_order();
				$current_pending = $whorder->getPendingOrder($item_id,$branch_id);
				$cur = 0;
				$st = 0;

				if(isset($stock->totalQty)){
					$st =$stock->totalQty ;
				}
				if(isset($current_pending->od_qty)){
					$cur =$current_pending->od_qty ;
				}
				$remaining = $st - $cur;

				if($remaining >= $qty){
					$valid = 1;
				}
				if($item_cls->data()->is_bundle == 1){
					$valid = 1;
					$bundle = new Bundle();
					$bundles = $bundle->getBundleItem($item_id);
					if($bundles){

						foreach($bundles as $bun){
							$bundle_needed_qty = $bun->child_qty * $qty;
							$pending_bundle_qty = $whorder->pendingBundles($bun->item_id_child,$branch_id);
							if($pending_bundle_qty && isset( $pending_bundle_qty->pending_qty )){
								$stock_bundle = $inv->getAllQuantity($bun->item_id_child,$branch_id);
								$st_bundle = 0;
								if(isset($stock_bundle->totalQty)){
									$st_bundle = $stock_bundle->totalQty ;
								}
								$remaining_bundle = $st_bundle - $pending_bundle_qty->pending_qty;
								if($remaining_bundle < 0) $remaining_bundle = 0;
								if($remaining_bundle < $bundle_needed_qty) $valid = 0;

							}
						}
					}
				}
				if(isset($is_composite->cnt) && !empty($is_composite->cnt)){
					$valid =1;
				}
				// check config
				if(Configuration::getValue('strict_order') == 2){
					$valid = 1;
				}
			}

			if($valid) return $remaining;
			return false;
		}
	}
	if(!function_exists('getpagenavigation')){ // some page have getpagenavigation
		function getpagenavigation($page, $total_pages, $limit, $stages) {
			if($page == 0) {
				$page = 1;
			}
			$prev = $page - 1;
			$next = $page + 1;
			$lastpage = ceil($total_pages / $limit);
			$LastPagem1 = $lastpage - 1;


			$paginate = '';
			if($lastpage > 1) {

				$paginate .= "<div style='padding:3px;' class='text-right imgonnastick'><ul class='pagination' >";

				if($page > 1) {
					$paginate .= "<li><a href='#'  class='paging' page='$prev' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></a></li>";
				} else {
					$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>PREV</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-left'></span></span></span></span></li>";
				}


				if($lastpage < 7 + ($stages * 2)) {
					for($counter = 1; $counter <= $lastpage; $counter++) {
						if($counter == $page) {
							$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						} else {
							$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
						}
					}
				} elseif($lastpage > 5 + ($stages * 2)) {

					if($page < 1 + ($stages * 2)) {
						for($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
							if($counter == $page) {
								$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
							} else {
								$paginate .= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";
							}
						}
						$paginate .= "<li><span style='padding:5px'>...</span></li>";
						$paginate .= "<li><a href='#'   class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
						$paginate .= "<li><a href='#' class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
					} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
						$paginate .= "<li><a href='#' class='paging' page='1'  style='padding:5px'>1</a></li>";
						$paginate .= "<li><a href='#' class='paging' page='2'  style='padding:5px'>2</a></li>";
						$paginate .= "<li><span style='padding:5px'>...</span></li>";
						for($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
							if($counter == $page) {
								$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
							} else {
								$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
							}
						}
						$paginate .= "<li><span  style='padding:5px'>...</span></li>";
						$paginate .= "<li><a href='#' class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
						$paginate .= "<li><a  href='#'  class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
					} else {
						$paginate .= "<li><a href='#' class='paging' page='1' style='padding:5px'>1</a></li>";
						$paginate .= "<li><a href='#' class='paging' page='2' style='padding:5px'>2</a></li>";
						$paginate .= "<li><span style='padding:5px'>...</span></li>";
						for($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
							if($counter == $page) {
								$paginate .= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
							} else {
								$paginate .= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";
							}
						}
					}
				}


				if($page < $counter - 1) {
					$paginate .= "<li><a href='#' class='paging' page='$next' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></a></li>";
				} else {
					$paginate .= "<li class='disabled'><span class='disabled' style='padding:5px'><span class='hidden-xs'>NEXT</span><span class='visible-xs'><span class='glyphicon glyphicon-chevron-right'></span></span></span></li>";
				}

				$paginate .= "</ul></div><div style='clear: both;'></div>";


			}
			// echo $total_pages.' Results';
			echo $paginate;
		}
	}
	if(!function_exists('getRegionOpt')){
		function getRegionOpt($selected_value=0){
			$arr_region = [
				'ARMM',
				'CAR',
				'NCR',
				'R1',
				'R2',
				'R3',
				'R4',
				'R4a',
				'R5',
				'R6',
				'R7',
				'R8',
				'R9',
				'R10',
				'R11',
				'R12',
				'R13'
			];
			$ret = "";
			foreach($arr_region as $reg){
				$selected="";
				if($reg == $selected_value){
					$selected = "selected";
				}
				$ret .= "<option value='$reg' $selected>$reg</option>";
			}
			return $ret;
		}
	}



	function getAdjustmentPriceGlobal($branch_id=0,$item_id = 0, $member_id = 0,$qty=0){
		$adjustment_class = new Item_price_adjustment();
		$branch_id_to=0;
		if($branch_id && $item_id && $member_id && $qty){
			$is_ret = true;
		} else {
			$is_ret = false;
			$branch_id = Input::get('branch_id');
			$item_id = Input::get('item_id');
			$member_id = Input::get('member_id');
			$qty = Input::get('qty');
			$branch_id_to = Input::get('branch_id_to');
		}

		$price_group_id = Input::get('price_group_id');


		$nadj = 0;
		$alladj = 0;
		$ctr_item = 1;
		$_SESSION['cart_item_counter'] = $ctr_item;
		// get current order not get stock
		// get stock
		// compare qty
		$valid = 0;
		$final_message = "";
		$remaining = 0;
		if($branch_id && $item_id && $qty){

			$availability = getReservedStocksGlobal($item_id,$branch_id,$qty);
			if($availability && $availability['message']){
				if(!$availability['success']){
					$final_message =  $availability['message'];
				} else {
					$valid = 1;
				}
				$remaining = $availability['remaining'];
			}
			if(Configuration::getValue('strict_order') == 2){
				$valid = 1;
			}
			if($member_id){
				$memberTerms = new Member_term();
				$memadj =$memberTerms->getAdjustment($member_id,$item_id);
				$total_member_adjustment = 0;
				if(count($memadj)){
					$alladjInd = 0;
					$alladjAbove = 0;
					foreach($memadj as $m){
						$madj = $m->adjustment;

						if($m->type == 1){ // for every
							if($qty < 1 && $qty != 0){
								if($m->qty == 1){
									$x = $qty / $m->qty;
								} else {
									$x = 0;
								}

							} else {
								$x = floor($qty / $m->qty);
							}

							$madj = $madj * $x;
							$total_member_adjustment += $madj;
							$alladjInd += $madj;
						} else if ($m->type == 2){ // above qty

							if($qty >= $m->qty){
								if($m->discount_type == 0){
									$alladjAbove += $madj;
									$total_member_adjustment += $madj;
								} else {
									$madj = $madj * $qty;
									$alladjAbove += $madj;
									$total_member_adjustment += $madj;
								}
							}


						}
					}
					if($alladjAbove){
						$alladj = $alladjAbove;
					} else if($alladjInd){
						$alladj = $alladjInd;
					}
				}
			}

			$adj = $adjustment_class->getAdjustment($branch_id,$item_id);
			if(isset($adj->adjustment)){
				$nadj += $adj->adjustment;
			} else {
				$nadj += 0;
			}
			if($price_group_id){
				$adj_price_group = $adjustment_class->getAdjustmentPriceGroup($item_id,$price_group_id);
				if(isset($adj_price_group->adjustment)){
					$nadj += $adj_price_group->adjustment;
				}
			}

			$prod = new Product();
			$price = $prod->getPrice($item_id);

			$branch_discount = new Branch_discount();
			$b_disc = $branch_discount->getDiscount($branch_id,$branch_id_to);
			$b_disc_amount = 0;
			if(isset($b_disc->discount) && !empty($b_disc->discount)){
				$b_disc_amount = $b_disc->discount / 100;

				$b_disc_amount = $price->price * $b_disc_amount;
			}
			if($b_disc_amount){
				$b_disc_amount  = ($b_disc_amount * $qty) * -1;
				$alladj += $b_disc_amount;
			}

			if(Configuration::getValue('discount_by_category') == 1){
				$memdis = new Member_category_discount();
				$memcategdis= $memdis->hasDiscount($item_id,$member_id);
				$totaladd = 0;

				$computed_price = $price->price;
				if(isset($memcategdis->discount_1)  && $memcategdis->discount_1){
					$discount_1 = $memcategdis->discount_1 * 0.01;
					$toadd = $computed_price * $discount_1;
					$totaladd += $toadd;
					$computed_price = $computed_price - $toadd;
					if(isset($memcategdis->discount_2)  && $memcategdis->discount_2){
						$discount_2 = $memcategdis->discount_2 * 0.01;
						$toadd = $computed_price * $discount_2;
						$computed_price = $computed_price - $toadd;
						$totaladd += $toadd;
						if(isset($memcategdis->discount_3)  && $memcategdis->discount_3){
							$discount_3 = $memcategdis->discount_3 * 0.01;
							$toadd = $computed_price * $discount_3;
							$computed_price = $computed_price - $toadd;
							$totaladd += $toadd;
							if(isset($memcategdis->discount_4)  && $memcategdis->discount_4){
								$discount_4 = $memcategdis->discount_4 * 0.01;
								$toadd = $computed_price * $discount_4;
								$totaladd += $toadd;
							}
						}

					}
				}
				$totaladd = $totaladd * -1;
				$alladj += $totaladd;
			}

		} else {
			$nadj += 0;
		}


		// get item freebie of item
		$item_freebie = new Item_freebie();

		$freebies = $item_freebie->getFreebies($item_id, $qty,$branch_id);

		$arr_freebies = [];
		if($freebies){
			foreach($freebies as $fr){
				$nadj_free =0;
				$adj_free = $adjustment_class->getAdjustment($branch_id,$fr->item_id);
				if(isset($adj_free->adjustment)){
					$nadj_free += $adj_free->adjustment;
				} else {
					$nadj_free += 0;
				}

				if($price_group_id){
					$adj_price_group_free = $adjustment_class->getAdjustmentPriceGroup($fr->item_id,$price_group_id);
					if(isset($adj_price_group_free->adjustment)){
						$nadj_free += $adj_price_group_free->adjustment;
					}
				}

				$fr->price += $nadj_free;
				$total = $fr->qty * $fr->price;

				$need_qty = $fr->need_qty;
				$discount = $fr->discount / 100;
				$total = $total * $discount;
				$total = number_format($total,2,".","");

				$multiplier =  $qty / $need_qty ;

				$multiplier = floor($multiplier);

				$fr->qty = $fr->qty *  $multiplier;

				$total = ($total * $multiplier);

				$arr_freebies[] = [
					'item_id' => $fr->item_id,
					'item_code' => $fr->item_code,
					'description' => $fr->description,
					'qty' => formatQuantity($fr->qty),
					'price' => $fr->price,
					'inv_qty' => formatQuantity($fr->inv_qty),
					'price' => $fr->price,
					'total' => $total
				];
			}
		}

		$group_adjustment_optional = [];
		if(Configuration::getValue('group_adjustment_optional') == 1){
			$grp_adj_opt = new Item_group_adjustment();
			$grp_list = $grp_adj_opt->getAdjustment($item_id);
			if($grp_list){
				$group_adjustment_optional = [];
				foreach($grp_list as $grp_item){
					$group_adjustment_optional[] = $grp_item;
				}

			}
		}

		$_SESSION['cart_item_counter'] = ($_SESSION['cart_item_counter'])? $_SESSION['cart_item_counter'] : 1;
		if($is_ret){
			return  $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
		} else {
			$output = $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
			echo json_encode(['data' => $output, 'freebies' => $arr_freebies, 'group_adjustment' => $group_adjustment_optional]);
		}

	}


	function getReservedStocksGlobal($item_id = 0, $branch_id = 0,$qty=0){
		$msg = "";
		$count_item = 1;
		if($branch_id && $item_id && $qty){
			$item_cls = new Product($item_id);
			$composite = new Composite_item();
			$is_composite = $composite->hasSpare($item_id);
			if($item_cls->data()->is_bundle != 1 && !(isset($is_composite->cnt) && !empty($is_composite->cnt))){

				$set = remainingSetGlobal($item_id,$branch_id);

				if($set['remaining'] && $set['remaining'] >= $qty  || $item_cls->data()->item_type != -1){
					return ['remaining' => $set['remaining'],'success' => true, 'message' => 'Stocks available'];
				} else {
					$msg = " Current Stock: "
						. formatQuantity($set['current_stock'])
						. " Pending Order: "
						.  formatQuantity($set['pending_order'])
						. " Pending in Service: "
						. formatQuantity($set['pending_service'])
						. " Available Stocks: "
						. formatQuantity($set['remaining']);

					$withDesign = "<ul class='list-group'>";
					$withDesign .= "<li class='list-group-item'>Current Quantity <strong>".formatQuantity($set['current_stock'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Pending Order <strong>".formatQuantity($set['pending_order'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Pending in Service <strong>".formatQuantity($set['pending_service'])."</strong></li>";
					$withDesign .= "<li class='list-group-item'>Available Stocks <strong>".formatQuantity($set['remaining'])."</strong></li>";
					$withDesign .= "</ul>";

					return ['remaining' => $set['remaining'],'success' => false, 'message' => $withDesign];
				}
			} else if ($item_cls->data()->is_bundle == 1){
				$bundle  = remainingBundleGlobal($item_id,$branch_id);
				$valid = 1;
				$arr_bundle = [];
				$rem = 0;
				foreach($bundle as $b){

					$needed_qty = $b['needed'] * $qty;
					if($b['remaining'] && $b['remaining'] >= $needed_qty){
						$arr_bundle[] = ['success' => true, 'message' => $b['item_code'] . ' *available'];

					} else {
						$msg = $b['item_code'] . " Needed: " . formatQuantity($needed_qty) . " Available: " . formatQuantity($b['remaining']). " Pending order: " . formatQuantity($b['pending_order']) . " Pending service: " . formatQuantity($b['pending_service']);
						$arr_bundle[] = ['success' => false, 'message' => $msg];

						$valid = 0;
					}
					$all = floor($b['remaining'] / $qty);
					if(!$rem || $rem > $all){
						$rem = $all;
					}
				}
				if($item_cls->data()->item_type == 1){
					$valid = 1;
				}
				if($valid){
					return ['remaining' => $rem,'success' => true, 'message' => 'Stocks available'];
				} else {
					$finalmsg = "<ul class='list-group'>";
					foreach($arr_bundle as $arr){
						if($arr['success']){
							$finalmsg .= "<li class='list-group-item text-success'>". $arr['message'] ."</li>";
						} else {
							$finalmsg .= "<li class='list-group-item text-danger'>". $arr['message'] ."</li>";
						}
					}
					$finalmsg .= "</ul>";
					return ['remaining' => $rem,'success' => false, 'message' => $finalmsg];
				}
			} else if (isset($is_composite->cnt) && !empty($is_composite->cnt)){
				$_SESSION['machine_qty'] = 0;
				$com = remainingCompositeGlobal($item_id,$branch_id);

				$valid = 1;
				$arr_bundle = [];
				$rem = 0;
				foreach($com as $b){
					$needed_qty = $b['needed'] * $qty;
					if($b['remaining'] && $b['remaining'] >= $needed_qty){
						$arr_bundle[] = ['success' => true, 'message' => $b['item_code'] . ' *available'];

					} else {
						$msg = $b['item_code'] . " Needed: " . formatQuantity($needed_qty) . " Available: " . formatQuantity($b['remaining']). " Pending order: " . formatQuantity($b['pending_order']) . " Pending service: " . formatQuantity($b['pending_service']);
						$arr_bundle[] = ['success' => false, 'message' => $msg];

						$valid = 0;
					}
					$all = floor($b['remaining'] / $qty);
					if(!$rem || $rem > $all){
						$rem = $all;
					}
				}
				if($_SESSION['machine_qty'] >= $qty ){

					$set = remainingSetGlobal($item_id,$branch_id);

					if($set['remaining'] && $set['remaining'] >= $qty ){
						$valid = 1;
						$rem = $_SESSION['machine_qty'];
					} else {

					}

				}

				if($item_cls->data()->item_type == 1 ){
					$valid = 1;
				}

				if($valid){
					return ['remaining' => $rem,'success' => true, 'message' => 'Stocks available'];
				} else {
					$finalmsg = "<ul class='list-group'>";
					foreach($arr_bundle as $arr){
						if($arr['success']){
							$finalmsg .= "<li class='list-group-item text-success'>". $arr['message'] ."</li>";
						} else {
							$finalmsg .= "<li class='list-group-item' text-danger>". $arr['message'] ."</li>";
						}
					}
					$finalmsg .= "</ul>";
					return ['remaining' => $rem,'success' => false, 'message' => $finalmsg];
				}
			}
		}
		return false;
	}

	function remainingSetGlobal($item_id = 0, $branch_id = 0){
		$inv = new Inventory();
		$rack_tags = new Rack_tag();
		$user = new User();
		$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$branch_id);
		if(isset($tags_ex->id) && !empty($tags_ex->id)){
			$excempt_tags = $tags_ex->tag_id;
		} else {
			$excempt_tags =0;
		}

		$item_for_order_cls = new Assemble_item_for_order();
		$item_for_order = $item_for_order_cls->getItem($item_id);
		$ass_subtract_total = 0;
		$total_all = 0;
		if(isset($item_for_order->item_id) && !empty($item_for_order->item_id)){
			$for_order = $inv->getAllQuantity($item_id,$branch_id,0);
			$total_all = $for_order->totalQty;
			$ass_subtract_total = $item_for_order->min_qty;
		}


		$stock = $inv->getAllQuantity($item_id,$branch_id,$excempt_tags);

		if($total_all){
			$is_still_allowed = $total_all - $stock->totalQty;
			if($is_still_allowed >= $ass_subtract_total){
				$stock->totalQty = $total_all - $ass_subtract_total;
			}
		}

		$whorder = new Wh_order();
		$item_service = new Service_request_item();
		$current_pending_order = $whorder->getPendingOrder($item_id,$branch_id);
		$current_pending_service = $item_service->getPendingRequest($item_id,$branch_id);
		$current_pending_in_bundle = $whorder->pendingInBundle($item_id,$branch_id);

		$current_pending_in_assemble = $whorder->pendingInAssemble($item_id,$branch_id);


		$cur = 0;
		$st = 0;
		$service_qty = 0;
		$pending_in_bundle = 0;
		$pending_in_assemble = 0;

		if(isset($current_pending_in_bundle->pending_qty)){
			$pending_in_bundle =$current_pending_in_bundle->pending_qty ;
		}

		if(isset($current_pending_in_assemble->pending_qty)){
			$pending_in_assemble =$current_pending_in_assemble->pending_qty ;
		}

		if(isset($stock->totalQty)){
			$st =$stock->totalQty ;
		}

		if(isset($current_pending_order->od_qty)){
			$cur =$current_pending_order->od_qty ;
		}

		if(isset($current_pending_service->service_qty)){
			$service_qty =$current_pending_service->service_qty ;
		}

		$remaining = $st - ($cur + $service_qty + $pending_in_bundle + $pending_in_assemble);

		return ['remaining' => $remaining, 'current_stock' => $st,'pending_order'=>$cur,'pending_service'=>$service_qty];
	}

	function remainingBundleGlobal($item_id = 0, $branch_id = 0){
		$bundle = new Bundle();
		$bundles = $bundle->getBundleItem($item_id);
		$inv = new Inventory();
		$whorder = new Wh_order();
		$item_service = new Service_request_item();
		$arr_inv = [];
		if($bundles){
			$_SESSION['cart_item_counter'] = count($bundles);
			$user = new User();
			$rack_tags = new Rack_tag();
			$tags_ex = $rack_tags->get_tags_ex('wh_orders',$user->data()->company_id,$branch_id);
			if(isset($tags_ex->id) && !empty($tags_ex->id)){
				$excempt_tags = $tags_ex->tag_id;
			} else {
				$excempt_tags =0;
			}
			foreach($bundles as $bun){
				$pending_bundle_qty = $whorder->pendingBundles($bun->item_id_child,$branch_id,$excempt_tags);
				if($pending_bundle_qty && isset( $pending_bundle_qty->pending_qty )){
					$stock_bundle = $inv->getAllQuantity($bun->item_id_child,$branch_id,$excempt_tags);
					$current_pending_service = $item_service->getPendingRequest($bun->item_id_child,$branch_id);
					$st_bundle = 0;
					$service_qty = 0;
					if(isset($stock_bundle->totalQty)){
						$st_bundle = $stock_bundle->totalQty ;
					}
					if(isset($current_pending_service->service_qty)){
						$service_qty =$current_pending_service->service_qty ;
					}
					$remaining_bundle = $st_bundle - ($pending_bundle_qty->pending_qty + $service_qty);
					if($remaining_bundle < 0) $remaining_bundle = 0;

					$arr_inv[] = ['item_code'=> $bun->item_code,'item_id_child' => $bun->item_id_child,'remaining' => $remaining_bundle,'current_stock' => $st_bundle,'pending_order' => $pending_bundle_qty->pending_qty,'pending_service' => $service_qty,'needed' => $bun->child_qty];
				}
			}
		}
		return $arr_inv;
	}

	function remainingCompositeGlobal($item_id = 0, $branch_id = 0){
		$composite = new Composite_item();
		$inv = new Inventory();
		$whorder = new Wh_order();
		$item_service = new Service_request_item();

		$spare_parts = $composite->getSpareparts($item_id);
		$arr_inv = [];
		if($spare_parts){
			//	$_SESSION['cart_item_counter'] = count($spare_parts);

			$assembled_qty = $inv->getAllQuantity($item_id,$branch_id);
			$ass_qty = 0;
			if(isset($assembled_qty->totalQty)){
				$ass_qty = $assembled_qty->totalQty ;
			}
			$_SESSION['machine_qty'] = $ass_qty;

			foreach($spare_parts as $spare){
				$pending_spare_qty = $whorder->pendingSpare($spare->item_id_raw,$branch_id); // get pending qty raw

				$assemble_spare_qty = 0; $whorder->spareWithAssemble($spare->item_id_raw,$branch_id);
				$assemble_qty_free = isset($assemble_spare_qty->assemble_qty) ? $assemble_spare_qty->assemble_qty : 0;
				$stock_composite = $inv->getAllQuantity($spare->item_id_raw,$branch_id);
				$st_composite = 0;
				$current_pending_service = $item_service->getPendingRequest($spare->item_id_raw,$branch_id);
				$service_qty = 0;
				if(isset($stock_composite->totalQty)){
					$st_composite = $stock_composite->totalQty ;
				}
				if(isset($current_pending_service->service_qty)){
					$service_qty =$current_pending_service->service_qty ;
				}
				$remaining_composite = ($st_composite+$assemble_qty_free) - ($pending_spare_qty->pending_qty + $service_qty);

				if($remaining_composite < 0) $remaining_composite = 0;
				$arr_inv[] = ['item_code' => $spare->item_code,'item_id_child' => $spare->item_id_raw,'remaining' => $remaining_composite,'current_stock' => $st_composite,'pending_order' => $pending_spare_qty->pending_qty,'pending_service' => $service_qty,'needed' => $spare->qty];
			}
		}
		return $arr_inv;
	}

	if(!function_exists('inventory_racking_spareparts')){
		function inventory_racking_spareparts($qty=0,$item_id = 0,$branch_id=0,$status=0){
			$inv = new Inventory_issue();
			$qty_racks = [];
			$insufficient = false;

			$inv_racks = $inv->get_racking($item_id,$branch_id,$status);
			if($inv_racks){

				if($inv_racks){
					foreach($inv_racks as $racking){

							$r_desc='';
							if($racking->rack_description){
								//		$r_desc = " (".$racking->rack_description.")";
							}
							if($racking->rack_qty > 0){
								if($qty > $racking->rack_qty){
									$qty = $qty - $racking->rack_qty;

									$qty_racks[] = array('rack' => $racking->rack . $r_desc,'rack_description' => $racking->rack_description,'stock_man' => $racking->stock_man,'qty' => $racking->rack_qty,'rack_id' => $racking->rack_id );
								} else {

									$qty_racks[] = array('rack' => $racking->rack . $r_desc,'rack_description' => $racking->rack_description,'stock_man' => $racking->stock_man,'qty' => $qty,'rack_id' => $racking->rack_id );
									$qty =0;
									break;
								}
							}

					}
				}
			}
			if($qty > 0){
				$qty_racks[] = array('rack' => 'Insufficient stock','qty' => $qty,'rack_id' => 0 );
				$insufficient = true;
			}

			return array('racking' => json_encode($qty_racks),'insufficient' => $insufficient);
		}
	}



?>